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

    public $location;
    public $location_specific;
    public $confinement;
    public $illness;
    public $study;
    public $study_other_purpose;
    public $commutation;

    public $remaining_credits;

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
            $this->handleLeaveCredits();


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

    public function handleLeaveCredits() {
        
        if($this->type == 1 || $this->type == 2) {
            $leaveType = LeaveType::where('id', $this->type)
                ->first();
            $leaveTypes = strtolower($leaveType->code);
            $records = EmployeeLeaveCard::where('employee_no', $this->employee_no)
                ->where('year', Carbon::now()->year)
                ->orderBy('year', 'asc') 
                ->get()
                ->last();
            $leaveTotalCredits = $records->{$leaveTypes . '_bal'} ?? 0;

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
    
        // Conditional rules based on $this->isMoreThanOne
        if ($this->isMoreThanOne) {
            $rules['to'] = 'required|date|after:from';
        } else {
            $rules['to'] = 'nullable|date';
        }
    
        // Conditional rules for 'location' based on $this->type
        if ($this->type == 1) {
            $rules['location'] = 'required|in:ph,abroad';
            $rules['location_specific'] = 'required';
        }
    
        // Conditional rules for 'confinement' and 'illness' based on $this->type
        if ($this->type == 3) {
            $rules['confinement'] = 'required';
            $rules['illness'] = 'required';
        }
    
        // Conditional rules for 'study' based on $this->type
        if ($this->type == 8) {
            $rules['study'] = 'required|in:completion_masters,examination,others';
    
            // If 'study' is 'others', make 'study_other_purpose' required
            if ($this->study == 'others') {
                $rules['study_other_purpose'] = 'required';
            }
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
                            // If $formatted_to is not null, check the date range
                            $query->whereBetween('from', [$formatted_from, $formatted_to])  // Leave starts within the requested range
                                ->orWhereBetween('to', [$formatted_from, $formatted_to])    // Leave ends within the requested range
                                ->orWhere(function ($subQuery) use ($formatted_from, $formatted_to) {
                                    // Full overlap (leave starts before and ends after the requested range)
                                    $subQuery->where('from', '<=', $formatted_from)
                                            ->where('to', '>=', $formatted_to);
                                });
                        } else {
                            // If $formatted_to is null, only check the 'from' date
                            $query->where('from', '=', $formatted_from)
                                ->orWhere('to', '=', $formatted_from);
                        }
                    })
                    ->where('status', '=', 'approved')
                    ->exists();


                if ($existingLeave) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops',
                        'message' => 'You already have an existing approved application during this period. Please select a different date.'
                    ]);
                }


                dd(123);


                if($this->type == 1 || $this->type == 2) {

                    $leaveType = LeaveType::where('id', $this->type)
                        ->first();
                    $leaveTypes = strtolower($leaveType->code);
    
                    $leaveTotalCredits = EmployeeLeaveCard::where('employee_no', $this->employee_no)
                        ->where('year', Carbon::now()->year)
                        ->orderBy('year', 'asc') 
                        ->get()
                        ->last();
    
                    $leaveTotalCredits = $leaveTotalCredits ? $leaveTotalCredits->{$leaveTypes . '_bal'} ?? 0 : 0;
    
                    $leaveEquiv = round((float) $daysCovered * 1.00, 3);
    
                    if($leaveTotalCredits == 0 || $leaveEquiv > $leaveTotalCredits) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops', 
                            'message' => 'Unfortunately, you have insufficient leave credits. You\'re applying for '.$daysCovered.' day(s), but only have ' . $leaveTotalCredits . ' remaining leave credits.'
                        ]);
                    }
    
                } else {
                    $leaveCredits = $leaveCreditsModel::where('leave_type_id', $this->type)
                        ->where('employee_no', $this->employee_no)
                        ->first();
    
                    if(is_null($leaveCredits) || $leaveCredits->credits == 0) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops', 
                            'message' => 'Unfortunately, you have no credits left for <b>' . $leaveTypeModel->name . '</b>.'
                        ]);
                    }
                    
                    if($daysCovered > $leaveCredits->credits) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'error',
                            'title' => 'Oops', 
                            'message' => 'Unfortunately, you have insufficient leave credits. You\'re applying for '.$daysCovered.' day(s), but only have ' . $leaveCredits->credits . ' remaining leave credits.'
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
                        'message' => 'Your application has been submitted. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
                    ]);

                    $user = EmployeeAccount::find($this->employee_id);
                    $message = 'Employee <strong>' . $this->employee_no . '</strong> has submitted an application for <strong>leave</strong>.';
                    $redirect = route('ess.leave');
                    $user->notify(new Notifications('info', $message, $redirect, 'admin'));

                    $this->resetExcept('employee_no', 'employee_id', 'leaveTypes');

                    return;

                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been updated. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
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
