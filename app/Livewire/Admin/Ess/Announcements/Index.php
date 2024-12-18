<?php

namespace App\Livewire\Admin\Ess\Announcements;

use App\Models\EmployeeAnnouncements;
use Livewire\Component;

class Index extends Component
{
    
    public $records = [];
    public $selected_id;
    protected $listeners = ['remove'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $records = EmployeeAnnouncements::all();
        $this->records = $records;
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

            $record = EmployeeAnnouncements::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Announcement ' . strtoupper($record->title) . ' deleted successfully' 
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
        return view('livewire.admin.ess.announcements.index');
    }
}
