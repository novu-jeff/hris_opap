<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function PHPUnit\Framework\isNull;

class Clock extends Component
{

    public $user_id;
    public $isClockedIn = false;
    public $isClockedOut = false;
    public $logs;

    protected $listeners = ['clockin', 'clockout'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_id;
        $this->user_id = $user_id;

        $today = Carbon::today();

        $records = EmployeeClockInOut::whereDate('created_at', $today)->first();

        if ($records) {
            if (!is_null($records->clock_in)) {
                $this->isClockedIn = true;
            }
        
            if (!is_null($records->clock_out)) {
                $this->isClockedOut = true;
            }
        }
    }

    public function clockin(bool $isNotify = true) {
        
        if($this->isAlreadyInOut('in')) {
            return;
        }

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'clockin';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            $timestamp = Carbon::now();

            EmployeeClockInOut::create([
                'employee_id' => $this->user_id,
                'clock_in' => $timestamp
            ]);
    
            $this->isClockedIn = true;
    
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Yey!', 
                'message' => 'You\'re clock in was recorded at ' . Carbon::parse($timestamp)->format('M d, Y h:i A')
            ]);
        }
    }

    public function clockout(bool $isNotify = true) {

        if($this->isAlreadyInOut('out')) {
            return;
        }

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'clockout';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            $today = Carbon::today();
            $timestamp = Carbon::now();
            
            EmployeeClockInOut::where('employee_id', $this->user_id)
                ->whereDate('created_at', $today)->update([
                    'clock_out' => $timestamp
                ]);

            $this->isClockedOut = true;

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Yey!', 
                'message' => 'You\'re clock out was recorded at ' . Carbon::parse($timestamp)->format('M d, Y h:i A')
            ]);
        }
    }
    
    public function isAlreadyInOut(string $type) {
    
        $today = Carbon::today();
    
        if($type == 'in') {
            $records = EmployeeClockInOut::whereDate('created_at', $today)->first();
    
            if ($records && !is_null($records->clock_in)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'info',
                    'title' => 'Oops', 
                    'message' => 'You have already clocked in at ' . Carbon::parse($records->clock_in)->format('M d, Y h:i A')
                ]);

                return true;
            }
        }
    
        if($type == 'out') {
            $records = EmployeeClockInOut::whereDate('created_at', $today)->first();
    
            if (!$records || is_null($records->clock_in)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'info',
                    'title' => 'Oops', 
                    'message' => 'Unable to clock out, you must clock in first!'
                ]);

                return true;

            }
    
            if (!is_null($records->clock_out)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'info',
                    'title' => 'Oops', 
                    'message' => 'You have already clocked out at ' . Carbon::parse($records->clock_out)->format('M d, Y h:i A')
                ]);

                return true;
            }
        }
    }

    public function showLogs() {
        $records = EmployeeClockInOut::where('employee_id', $this->user_id)
            ->get();

        $this->logs = $records;
        
        $this->dispatch('showModal', [
            'modal' => 'logs_modal'
        ]);

    }

    public function render()
    {
        return view('livewire.employee.clock');
    }
}
