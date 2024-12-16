<?php

namespace App\Livewire\Admin\Ess\ProfileApproval;

use App\Models\EmployeeUpdatePersonal;
use Livewire\Component;

class Index extends Component
{

    public $selected_id;
    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $records = EmployeeUpdatePersonal::all();     
        return $this->records = $records;
    }

    public function remove(bool $isNotify = true, string $employee_no = null) {
        $this->dispatch('reinitializeDataTable');
    
        if ($isNotify) {
            $this->selected_id = $employee_no;
    
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'This action cannot be undone!',
                'action' => 'remove',
            ]);
    
            return;
        }
    
        // Fetch the employee record
        $record = EmployeeUpdatePersonal::with([
            'education', 
            'parents', 
            'children', 
            'employment_history', 
            'civil_service', 
            'trainings', 
            'others', 
            'skills', 
        ])->where('employee_no', $this->selected_id)->first();
    
        if (!$record) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => false,
                'message' => 'Error: Employee record does not exist!',
            ]);
        }
    
        try {
            // Delete related records
            $relatedRelations = [
                'education', 'parents', 
                'children', 'employment_history', 'civil_service', 
                'trainings', 'others', 'skills'
            ];
    
            foreach ($relatedRelations as $relation) {
                if ($record->$relation) {
                    $record->$relation()->delete();
                }
            }
    
            // Delete the main employee record
            $record->delete();
    
            // Reload records and notify success
            $this->loadRecords();
    
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'id' => $this->selected_id,
                'isRemoveRowDT' => true,
                'message' => 'Profile update has been removed.',
            ]);
        } catch (\Exception $e) {
            // Handle any errors during deletion
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error!',
                'isRemoveRowDT' => false,
                'message' => 'An error occurred while deleting the record: ' . $e->getMessage(),
            ]);
        }
    }
    

    public function render()
    {
        return view('livewire.admin.ess.profile-approval.index');
    }
}
