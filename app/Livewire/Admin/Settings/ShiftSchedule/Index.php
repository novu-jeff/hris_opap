<?php

namespace App\Livewire\Admin\Settings\ShiftSchedule;

use App\Models\CompanyInformation;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\DB;
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

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this shift schedule. Once this action is completed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = ShiftSchedule::with('employees')->find($this->selected_id);
            
            if($record) {
                
                if ($record->employees->isNotEmpty()) {
                    $message = 'Unable to delete this shift because there are ' . $record->employees->count() . ' employee' . ($record->employees->count() > 1 ? 's' : '') . ' linked to this shift.';
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

        $model = ShiftSchedule::query();

        if ($this->search) {
            $this->resetPage(); 
            $records = $model->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('shift_duration', 'like', '%' . $this->search . '%')
                ->orWhere('work_setup', 'like', '%' . $this->search . '%');
        }

        $records = $model->latest()->paginate($this->entries);


        return view('livewire.admin.settings.shift-schedule.index', [
            'records' => $records
        ]);
    }
}
