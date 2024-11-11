<?php

namespace App\Livewire\Employee\Leave;

use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Apply extends Component
{

    public $type;
    public $from;
    public $to;
    public $reason;
    public $record_id;
    public $user_id;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_id;
        $this->user_id = $user_id;
 
        if(!is_null($this->record_id)) {
            $record = EmployeeLeave::where('id', $this->record_id)
                ->where('employee_id', $user_id)
                ->first();
        
            if(!$record) {
                return redirect()
                    ->route('employee.leave');
            }

            $this->type = $record->type;
            $this->from = $record->from;
            $this->to = $record->to;
            $this->reason = $record->reason;
        }

        
    }

    public function rules() {
        return [
            'type' => 'required|in:casual,medical,unpaid,emergency,sick',
            'reason' => 'required',
            'from' => 'required|date|after:today',
            'to' => 'required|date|after:from'
        ];
    }

    public function message() {
        return [];
    }

    public function save(bool $isNotify = true) {
        
        $this->validate();

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {

            try {
                
                $from = Carbon::parse($this->from);
                $to = Carbon::parse($this->to);
                $consumed_hours = $from->diffInHours($to);

                $model = EmployeeLeave::class;
                $pending = $model::where('employee_id', $this->user_id)
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
                    'employee_id' => $this->user_id,
                    'type' => $this->type,
                    'reason' => $this->reason,
                    'from' => $from->format('Y-m-d'),
                    'to' => $to->format('Y-m-d'),
                    'measurement' => 'full day',
                    'consumed_hours' => $consumed_hours 
                ]);

                if(is_null($this->record_id)) {

                    $this->resetExcept('user_id');

                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been submitted. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding'
                    ]);

                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been updated.'
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
