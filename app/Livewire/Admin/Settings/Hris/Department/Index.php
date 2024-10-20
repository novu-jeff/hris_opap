<?php

namespace App\Livewire\Admin\Settings\Hris\Department;

use App\Models\DepartmentCenters;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = DepartmentCenters::with('cost_center')->get();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.department.index');
    }
}
