<?php

namespace App\Livewire\Admin\Settings\Hris\EmployeeStatus;

use App\Models\EmployeeStatus;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = EmployeeStatus::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.employee-status.index');
    }
}
