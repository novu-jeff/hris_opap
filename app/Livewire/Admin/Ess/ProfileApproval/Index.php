<?php

namespace App\Livewire\Admin\Ess\ProfileApproval;

use App\Models\EmployeeUpdatePersonal;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function remove(bool $isNotify = true, string $employee_no = null) {

        if (Gate::denies('write employee-profile-approval')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }
    
        if ($isNotify) {
            $this->selected_id = $employee_no;
    
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'Please be informed that you are about to delete this employee profile update application <b>' . strtoupper($employee_no) . '</b>. Once this action is processed, it cannot be undone or reversed!',
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

        $model = EmployeeUpdatePersonal::query();
        
        if ($this->search) {
            $this->resetPage(); 
            $records = $model->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
        }

        $records = $model->paginate($this->entries);

        return view('livewire.admin.ess.profile-approval.index', [
            'records' => $records
        ]);
    }
}
