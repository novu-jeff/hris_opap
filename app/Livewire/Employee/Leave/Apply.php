<?php

namespace App\Livewire\Employee\Leave;

use App\Models\EmployeeLeave;
use App\Models\LeaveType;
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
    public $user_id;
    public $leaveTypes;
    public $isMoreThanOne = null;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $this->leaveTypes = LeaveType::all();

        $user_id = Auth::user()->employee_no;
        $this->user_id = $user_id;
 
        if(!is_null($this->record_id)) {
            $records = EmployeeLeave::where('id', $this->record_id)
                ->where('employee_no', $user_id)
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

            $this->from = $records->from;
            $this->to = $records->to;
            $this->reason = $records->reason;
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

    public function rules() {
        $rules = [
            'duration' => 'required',
            'type' => 'required|exists:leave_types,id',
            'reason' => 'required',
            'from' => 'required|date|after:today',
            'to' => 'required|date',
        ];
    
        if ($this->isMoreThanOne) {
            $rules['to'] = 'required|date|after:from';
        } else {
            $rules['to'] = 'nullable|date';
        }
    
        return $rules;
    }
    

    public function message() {
        return [
            'type.exists' => 'Leave type does not exists.'
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
                $consumed_hours = $to ? $to->diffInHours($from) : 24;

                $model = EmployeeLeave::class;
                $pending = $model::where('employee_no', $this->user_id)
                    ->where('status', false)
                    ->count();

                $max_pending = env('MAX_PENDING_LEAVE_APPLICATION');

                if($pending >= $max_pending) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops', 
                        'message' => 'Unfortunately, you have reached your maximum limit for leave applications. You currently have ' . $pending . ' applications awaiting approval.'
                    ]);
                }

                $model::updateOrCreate([
                    'id' => $this->record_id,
                ], [
                    'employee_no' => $this->user_id,
                    'leave_id' => $this->type,
                    'reason' => $this->reason,
                    'from' => $from->format('Y-m-d'),
                    'to' => $to ? $to->format('Y-m-d') : null,
                    'measurement' => 'full day',
                    'consumed_hours' => $consumed_hours 
                ]);

                if(is_null($this->record_id)) {

                    $this->resetExcept('user_id', 'leaveTypes');

                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been submitted. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
                    ]);

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
