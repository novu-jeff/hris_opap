<?php

namespace App\Livewire\Admin\Ess\BusinessSlip;

use App\Models\EmployeeBusinessSlip;
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
        $records = DB::table('employee_business_slips')
            ->leftJoin('employee_personal', 'employee_business_slips.employee_no', '=', 'employee_personal.employee_no')
            ->leftJoin('employee_information', 'employee_business_slips.employee_no', '=', 'employee_information.employee_no')
            ->leftJoin('positions', 'employee_information.position_id', '=', 'positions.id')
            ->leftJoin('sections', 'employee_information.section_id', '=', 'sections.id')
            ->leftJoin('branches', 'sections.branch_id', '=', 'branches.id')
            ->leftJoin('departments', 'sections.department_id', '=', 'departments.id')
            ->select(
                'employee_business_slips.*',
                'employee_personal.firstname',
                'employee_personal.middlename',
                'employee_personal.lastname',

                'positions.code as position_code',
                'positions.name as position_name',
                
                'sections.name as section_name',
                'sections.code as section_code',

                'branches.name as branch_name',
                'branches.code as branch_code',

                'departments.name as department_name',
                'departments.code as department_code',

            )
            ->where('employee_business_slips.status',  $this->status);
            
        if(!is_null($id)) {
            $records->where('employee_business_slips.id', $id);
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

            EmployeeBusinessSlip::where('id', $this->selected_id)
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

            $record = EmployeeBusinessSlip::with('employment')->where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();
            
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

            $record = EmployeeBusinessSlip::find($this->selected_id);
                
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
        return view('livewire.admin.ess.business-slip.index');
    }
}
