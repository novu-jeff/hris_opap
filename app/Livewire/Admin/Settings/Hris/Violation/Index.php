<?php

namespace App\Livewire\Admin\Settings\Hris\Violation;

use App\Models\Violations;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    protected $listeners = ['remove'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';


    public function remove(bool $isNotify = true, int $id = null) {

        if (Gate::denies('write violations')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this violation. Once this action is processed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = Violations::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Violation ' . strtoupper($record->name) . ' deleted successfully' 
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

        $model = Violations::query();

        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where('name', 'like', '%' . $this->search . '%');
        }
        
        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.settings.hris.violation.index', [
            'records' => $records
        ]);
    }
}
