<?php

namespace App\Livewire\Admin\Ess\Atro;

use App\Models\EmployeeAtro;
use Illuminate\Support\Facades\DB;
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
        $records = DB::table('employee_atro')
            ->leftJoin('employee_personal', 'employee_atro.employee_no', '=', 'employee_personal.employee_no')
            ->leftJoin('employee_information', 'employee_atro.employee_no', '=', 'employee_information.employee_no')
            ->select(
                'employee_atro.*',
                'employee_personal.firstname',
                'employee_personal.middlename',
                'employee_personal.lastname',
            )
            ->where('employee_atro.status',  $this->status);
            
        if(!is_null($id)) {
            $records->where('employee_atro.id', $id);
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

            EmployeeAtro::where('id', $this->selected_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'denied'
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

            EmployeeAtro::where('id', $this->selected_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approve'
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

            $record = EmployeeAtro::find($this->selected_id);
                
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
        return view('livewire.admin.ess.atro.index');
    }
}
