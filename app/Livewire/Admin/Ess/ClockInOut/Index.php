<?php

namespace App\Livewire\Admin\Ess\ClockInOut;

use App\Models\EmployeeClockInOut;
use Livewire\Component;

class Index extends Component
{

    public $records;

    public function mount() {
        $records = EmployeeClockInOut::with('information.personal')->get();
        $this->records = $records;
    }

    public function render()
    {
        return view('livewire.admin.ess.clock-in-out.index');
    }
}
