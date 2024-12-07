<?php

namespace App\Livewire\Admin\Ess\Leave;

use App\Models\EmployeeLeave;
use Livewire\Component;

class Index extends Component
{
 
    public $status;
    public $records;
    public $view_records;
    public $selected_id;
    public $activeTab = 'pending';
    protected $listeners = ['remove', 'rejected', 'granted'];


    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords(int $id = null) {
        $records = EmployeeLeave::with('employment', 'employee', 'leave_type')
            ->where('status', $this->status);
            
        if(!is_null($id)) {
            $records->where('id', $id);
            return $this->view_records = $records->first();
        }
        return $this->records = $records->get();
    }

    public function view(int $id) {
        $this->selected_id = $id;
        $this->loadRecords($id);
        if(!is_null($this->view_records)) {
            return $this->dispatch('showModal', [
                'modal' => 'showModal', 
            ]);
        }
    }

    public function rejected(bool $isNotify = true) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'rejected';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            EmployeeLeave::where('id', $this->selected_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected'
                ]);

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been rejected'
            ]);


        }
    }

    public function granted(bool $isNotify = true) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'granted';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            $record = EmployeeLeave::with('employment')->where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();

            if ($record && $record->employment->leave_credits <= 0) {
                return $this->dispatch('alert', [
                    'id' => $this->selected_id,
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Ooops', 
                    'message' => 'Unable to grant leave because there\'s no leave credit left to this employee.'
                ]);
            }

            $record->employment->leave_credits -= 1;
            $record->employment->save();

            $record->update([
                'status' => 'granted'
            ]);

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been granted'
            ]);


        }
    }

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeLeave::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Leave Application #' . strtoupper(format_id($record->id, 6)) . ' deleted successfully.' 
                ]);
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }
    }
 
    public function render()
    {
        return view('livewire.admin.ess.leave.index');
    }
}
