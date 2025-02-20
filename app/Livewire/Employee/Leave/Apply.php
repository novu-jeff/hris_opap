<?php

namespace App\Livewire\Employee\Leave;

use App\Models\EmployeeAccount;
use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Apply extends Component
{

    public $type;
    public $duration;
    public $from;
    public $to;
    public $reason;
    public $record_id;
    public $employee_no;
    public $employee_id;
    public $leaveTypes;
    public $isMoreThanOne = null;
    public $notification;
    public $accepts_autwopay;

    public $location;
    public $location_specific;
    public $confinement;
    public $illness;
    public $study;
    public $study_other_purpose;
    public $commutation;

    public $remaining_credits;
    public bool $isDurationDisabled = false;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $this->leaveTypes = LeaveType::all();

        $employee_no = Auth::user()->employee_no;
        $employee_id = Auth::user()->id;

        $this->employee_no = $employee_no;
        $this->employee_id = $employee_id;
 
        if(!is_null($this->record_id)) {
            
            $records = EmployeeLeave::where('id', $this->record_id)
                ->where('employee_no', $employee_no)
                ->first();
        
            if(!$records) {
                return redirect()
                    ->route('employee.leave');
            }


            $this->type = $records->leave_id;
            
            if(is_null($records->to)) {
                $this->duration = 1;
            } else {
                $this->duration = 2;
            }


            $this->selectDuration();
            $this->handleLeaveCredits($this->duration);


            $this->from = $records->from;
            $this->to = $records->to;
            $this->reason = $records->reason;
            $this->location = $records->location;
            $this->location_specific = $records->location_specific;
            $this->confinement = $records->confinement;
            $this->illness = $records->illness;
            $this->study = $records->study;
            $this->study_other_purpose = $records->study_other_purpose;
            $this->commutation = $records->commutation;
        }

        
    }

    public function selectDuration() {
        if(!empty($this->duration)) {
            if($this->duration == 2) {
                return $this->isMoreThanOne = true;
            } 
    
            return $this->isMoreThanOne = false;
        } else {
            return $this->isMoreThanOne = null;
        }
    }

    public function handleLeaveCredits(int $duration = null) {

        $this->reset('duration', 'isDurationDisabled', 'isMoreThanOne');
    
        $leaveType = LeaveType::where('id', $this->type)
            ->first();
        $leaveTypes = strtolower($leaveType->code ?? null);

        $this->duration = $duration;

        if($this->type == 1 || $this->type == 2) {
            $records = EmployeeLeaveCard::where('employee_no', $this->employee_no)
                ->where('year', Carbon::now()->year)
                ->orderBy('year', 'asc') 
                ->get()
                ->last();
            $leaveTotalCredits = $records->{$leaveTypes . '_bal'} ?? 0;

        } else if($this->type == 3) {
            
            $this->duration = 2;
            $this->isDurationDisabled = true;
            $this->isMoreThanOne = true;

            $records = EmployeeLeaveCard::where('employee_no', $this->employee_no)
                ->where('year', Carbon::now()->year)
                ->orderBy('year', 'asc') 
                ->get()
                ->last();
                
            $leaveTotalCredits = $records->vl_bal ?? 0;

            if($leaveTotalCredits > 10) {
                $leaveTotalCredits = 5;
            }
            
        } else {
            $records = LeaveCredits::where('employee_no', $this->employee_no)
                ->where('leave_type_id', $this->type)
                ->first();

            $leaveTotalCredits = $records->credits ?? 0;
            
        }



        $this->remaining_credits = $leaveTotalCredits;
    }

    public function rules() {
        $rules = [
            'duration' => 'required',
            'type' => 'required|exists:leave_types,id',
            'from' => 'required|date|after:today',
            'to' => 'required|date',
            'commutation' => 'required|in:yes,no'
        ];
    
        // Adjust 'to' field validation based on isMoreThanOne
        if ($this->isMoreThanOne) {
            $rules['to'] = ['required', 'date', 'after:from'];
        } else {
            $rules['to'] = ['nullable', 'date'];
        }
    
        // Conditional validation based on type
        switch ($this->type) {
            case 1: // Location required for type 1
                $rules['location'] = 'required|in:ph,abroad';
                $rules['location_specific'] = 'required';
                break;
    
            case 2: // Confinement and illness required for type 2
                $rules['confinement'] = 'required';
                $rules['illness'] = 'required';
                break;
    
            case 3: // Ensure 'to' is at least 5 days after 'from'
                $rules['from'] = ['required', 'date', 'after:today'];
                $rules['to'] = [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        if (strtotime($value) - strtotime($this->from) < 4 * 86400) { // Ensure at least 5 days total
                            $fail('requires atleast 5 days to spend');
                        }
                    }
                ];
                break;
    
            case 8: // Study leave
                $rules['study'] = 'required|in:completion_masters,examination,others';
                if ($this->study === 'others') {
                    $rules['study_other_purpose'] = 'required';
                }
                break;
        }
    
        return $rules;
    }
    
    
    

    public function messages() {
        return [
            'type.exists' => 'Leave type does not exists.',
            'location_specific.required' => 'The specific location field is required.'
        ];
    }

    public function save(bool $isNotify = true) {
        
        $this->validate();
        
        if($isNotify) {
            
            $title = 'Are you sure to continue?';
            $message = 'Yes, I am sure that all the information I have provided is accurate and true. This ensures that there will be no issues as we proceed.';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            try {
                
                $from = Carbon::parse($this->from);
                $to = $this->isMoreThanOne ? Carbon::parse($this->to) : null;

                if ($to) {
                    $daysCovered = $from->diffInDays($to) + 1; 
                } else {
                    $daysCovered = 1; 
                }

                $employeeLeaveModel = EmployeeLeave::class;
                $leaveTypeModel = LeaveType::find($this->type);
                $leaveCreditsModel = LeaveCredits::class;

                $pending = $employeeLeaveModel::where('employee_no', $this->employee_no)
                    ->where('status', false)
                    ->count();

                $leaveCredits = $leaveCreditsModel::where('leave_type_id', $this->type)
                    ->where('employee_no', $this->employee_no)
                    ->first();

                $max_pending = env('MAX_PENDING_LEAVE_APPLICATION');

                // maximum pending leaves
                if($pending >= $max_pending) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops', 
                        'message' => 'Unfortunately, you have reached your maximum limit for leave applications. You currently have ' . $pending . ' applications awaiting approval.'
                    ]);
                }

                $formatted_from = Carbon::parse($from)->format('Y-m-d');
                $formatted_to = isset($to) ? Carbon::parse($to)->format('Y-m-d') : null;

                // Check if the leave date already exists in the database (excluding approved status)
                $existingLeave = $employeeLeaveModel::where('employee_no', $this->employee_no)
                    ->where(function ($query) use ($formatted_from, $formatted_to) {
                        if ($formatted_to) {
                            // Check for any overlap when both "from" and "to" are provided
                            $query->where(function ($subQuery) use ($formatted_from, $formatted_to) {
                                $subQuery->whereBetween('from', [$formatted_from, $formatted_to]) // Starts within range
                                    ->orWhereBetween('to', [$formatted_from, $formatted_to]) // Ends within range
                                    ->orWhere(function ($overlapQuery) use ($formatted_from, $formatted_to) {
                                        // Existing leave fully covers the new leave
                                        $overlapQuery->where('from', '<=', $formatted_from)
                                                    ->where('to', '>=', $formatted_to);
                                    })
                                    ->orWhere(function ($containedQuery) use ($formatted_from, $formatted_to) {
                                        // New leave is fully within an existing leave
                                        $containedQuery->where('from', '<=', $formatted_to)
                                                    ->where('to', '>=', $formatted_from);
                                    });
                            });
                        } else {
                            // If "to" is null, check if any leave already exists on the "from" date
                            $query->where('from', '=', $formatted_from);
                        }
                    })
                    ->where('status', 'pending')
                    ->where('isDeleted', false)
                    ->exists();


                if ($existingLeave) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops',
                        'message' => 'You already have an existing application during this period. Please select a different date.'
                    ]);
                }

                if (in_array($this->type, [1, 2, 3])) {
                
                    $leaveType = LeaveType::find($this->type);
                    $leaveCode = strtolower($leaveType->code);
                
                    $leaveCard = EmployeeLeaveCard::where('employee_no', $this->employee_no)
                        ->where('year', Carbon::now()->year);

                    if(!$leaveCard->exists()) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops',
                            'message' => 'Unfortunately, vacation leave (VL), sick leave (SL) and mandatory / forced leave (MFL) are not available. Please try again later.'
                        ]);
                    }

                    $leaveTotalCredits = $leaveCard->orderBy('year', 'asc')
                        ->get()
                        ->last();

                    if($this->type == 1 || $this->type == 2) {
                        $leaveTotalCredits = $leaveTotalCredits ? (float) $leaveTotalCredits->{strtolower($leaveCode) . '_bal'} ?? 0 : 0;
                    } else {
                        $leaveTotalCredits = $leaveTotalCredits ? (float) $leaveTotalCredits->vl_bal ?? 0 : 0;
                    }

                    
                    $leaveEquiv = round((float) $daysCovered * 1.00, 3);
                
                    if($this->type == 3 && $leaveTotalCredits <= 10) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops',
                            'message' =>  'Unfortunately, unable to use <b>mandatory or forced leave</b> because you only have <b>' . $leaveTotalCredits . '</b> credits left.'
                        ]);
                    }

                    if (in_array($this->type, [1, 2]) && !$this->accepts_autwopay) {
                        if ($leaveTotalCredits == 0 || $leaveEquiv > $leaveTotalCredits) {
                            $this->accepts_autwopay = true;
                            return $this->dispatch('showConfirmation', [
                                'title' => 'Please be Informed',
                                'message' => "Unfortunately, your leave credits are insufficient. You are requesting {$daysCovered} day(s) of leave, but you only have {$leaveTotalCredits} remaining. You may still proceed with your request, but please note that this will be considered as Absence Without Pay (AUT w/o pay).",
                                'action' => 'save'
                            ]);
                        }
                    }
                } else {
                    $leaveCredits = $leaveCreditsModel::where('leave_type_id', $this->type)
                        ->where('employee_no', $this->employee_no)
                        ->first();
                
                    if (!$leaveCredits || $leaveCredits->credits == 0) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops',
                            'message' => "Unfortunately, you have no credits left for <b>{$leaveTypeModel->name}</b>."
                        ]);
                    }
                
                    if ($daysCovered > $leaveCredits->credits) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops',
                            'message' => "Unfortunately, you have insufficient leave credits. You're applying for {$daysCovered} day(s), but only have {$leaveCredits->credits} remaining leave credits."
                        ]);
                    }
                }
                
                
                $employeeLeaveModel::updateOrCreate([
                    'id' => $this->record_id,
                ], [
                    'employee_no' => $this->employee_no,
                    'leave_id' => $this->type,
                    'from' => $from->format('Y-m-d'),
                    'to' => $to ? $to->format('Y-m-d') : null,
                    'location' => $this->location ?? null,
                    'location_specific' => $this->location_specific ?? null,
                    'confinement' => $this->confinement ?? null,
                    'illness' => $this->illness ?? null,
                    'study' => $this->study ?? null,
                    'study_other_purpose' => $this->study_other_purpose ?? null,
                    'commutation' => $this->commutation ?? null,
                ]);

                if(is_null($this->record_id)) {

                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been successfully submitted. You can now download the form to begin obtaining the required signatures. Once completed, please send a copy back to us. Thank you for your cooperation.'
                    ]);

                    $user = EmployeeAccount::find($this->employee_id);
                    $message = 'Employee <strong>' . $this->employee_no . '</strong> has submitted an application for <strong>leave</strong>.';
                    $redirect = route('ess.leave');
                    $user->notify(new Notifications('info', $message, $redirect, 'admin'));

                    $this->resetExcept('employee_no', 'employee_id', 'leaveTypes');
                    $this->accepts_autwopay = false;

                    return;

                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been successfully updated. You can now download the form to begin obtaining the required signatures. Once completed, please send a copy back to us. Thank you for your cooperation.'
                    ]);
                }

            } catch (\Exception $e) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.employee.leave.apply');
    }
}
