<?php

namespace App\Livewire\Admin\Settings\EmployeeSchedule;

use App\Models\EmployeeSchedule;
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
        $records = EmployeeSchedule::all();  // Get all employee schedules
    
        $data = [];
    
        foreach ($records as $record) {
            // Create an object for each record
            $obj = new \stdClass();
            $obj->id = $record->id;  // Add the 'id' property
            $obj->name = $record->name;
    
            // Get the days that are true (1) and store them in an array
            $days = [];
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                if ($record->$day == 1) {
                    $days[] = ucfirst($day);  // Add the day name to the array if it's true (1)
                }
            }
    
            $obj->days = implode(', ', $days);  // Implode the array into a comma-separated string
    
            // Add the object to the data array
            $data[] = $obj;
        }
    
        $this->records = $data;
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

            $record = EmployeeSchedule::find($this->selected_id);
                
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
        return view('livewire.admin.settings.employee-schedule.index');
    }
}
