<?php

namespace App\Livewire\Admin\Settings\ShiftSchedule;

use App\Models\CompanyInformation;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public $selected_id;
    public $records;

    protected $listeners = ['remove']; 

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $this->records = ShiftSchedule::all();
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

            $record = ShiftSchedule::find($this->selected_id);
                
            if($record) {
                
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
        return view('livewire.admin.settings.shift-schedule.index');
    }
}
