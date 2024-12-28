<?php

namespace App\Livewire\Admin\Ess\Leave;

use App\Models\EmployeeLeave;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
 
    use WithPagination;

    public $status;
    public $view_records;
    public $selected_id;
    public $activeTab = 'pending';
    protected $listeners = ['remove', 'rejected', 'granted'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function view(int $id) {
        $this->selected_id = $id;
        $this->loadRecords($id);
        if(!is_null($this->view_records)) {
            return $this->dispatch('showModal', [
                'modal' => 'showModal', 
            ]);
        }
    }

    public function loadRecords(int $id) {
        $this->view_records = EmployeeLeave::with('employment', 'employee', 'leave_type')
            ->where('id', $id)
            ->first();
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
            
            if ($record && $record->employment && (is_null($record->employment->leave_credits) || $record->employment->leave_credits <= 0)) {
                return $this->dispatch('alert', [
                    'id' => $this->selected_id,
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Ooops',
                    'message' => 'Unable to grant leave because there\'s no leave credit left to this employee.'
                ]);
            }
            
            // Deduct 1 leave credit and save the Employment model
            $employment = $record->employment;
            $employment->leave_credits -= 1;
            $employment->save();
            
            // Update the EmployeeLeave record's status
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
       
        $model = EmployeeLeave::with('employment', 'employee', 'leave_type')
            ->where('status', $this->status);

        if ($this->search) {
            $this->resetPage(); 

            $records = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('employee', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        } else {
            $records = $model;
        }

        $records = $records->latest()->paginate($this->entries);


        return view('livewire.admin.ess.leave.index', [
            'records' => $records
        ]);
    }
}
