<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeAnnouncements;
use Livewire\Component;

class Announcements extends Component
{

    public $record_id;
    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $records = EmployeeAnnouncements::class;

        if(!is_null($this->record_id)) {
            $records = $records::where('id', $this->record_id)->first();

            if(!$records) {
                return redirect()->route('employee.announcements.index');
            }

            return $this->records = $records;

        }

        return $this->records = $records::all();
    }

    public function render()
    {
        return view('livewire.employee.announcements');
    }
}
