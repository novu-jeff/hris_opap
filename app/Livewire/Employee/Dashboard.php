<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeAnnouncements;
use Livewire\Component;

class Dashboard extends Component
{

    public $announcements;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $this->announcements = EmployeeAnnouncements::orderBy('created_at', 'desc')->take(10)->get();
        $this->announcements = [];
    }

    public function render()
    {
        return view('livewire.employee.dashboard');
    }
}
