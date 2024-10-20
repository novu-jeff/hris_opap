<?php

namespace App\Livewire\Admin\Settings\Hris\CostCenter;

use App\Models\CostCenters;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = CostCenters::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.cost-center.index');
    }
}
