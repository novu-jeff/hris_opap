<?php

namespace App\Livewire\Employee;

use App\Models\LeaveCredits;
use Livewire\Component;

class Credits extends Component
{

    public $records;

    public function mount() {

        $records = LeaveCredits::with('leave')->get() ?? [];
        
        return $this->records = $records;

    }

    public function render()
    {
        return view('livewire.employee.credits');
    }
}
