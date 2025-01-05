<?php

namespace App\Livewire\Admin\Settings\EmployeeSchedule;

use App\Models\EmployeeSchedule;
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

        if (Gate::denies('write employee-schedule')) {
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
            $message = 'Please be informed that you are about to delete this employee schedule. Once this action is processed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeSchedule::with('employees')->find($this->selected_id);
                
            if($record) {

                if ($record->employees->isNotEmpty()) {
                    $message = 'Unable to delete this schedule because there are ' . $record->employees->count() . ' employee' . ($record->employees->count() > 1 ? 's' : '') . ' linked to this schedule.';
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'warning',
                        'title' => 'Please be informed!',
                        'message' => $message
                    ]);
                }

                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Shift schedule ' . strtoupper($record->name) . ' deleted successfully' 
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
        $model = EmployeeSchedule::query();

        if ($this->search) {
            $this->resetPage();
        
            $model->where('name', 'like', '%' . $this->search . '%');

        }

        $records = $model->latest()->paginate($this->entries);

        $records->getCollection()->transform(function ($record) {
            $record->days = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                ->filter(fn($day) => $record->$day == 1)
                ->map(fn($day) => ucfirst($day))
                ->implode(', ');

            return $record;
        });

        return view('livewire.admin.settings.employee-schedule.index', [
            'records' => $records, 
        ]);
    }

}
