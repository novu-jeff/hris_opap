<?php

namespace App\Livewire\Employee;

use App\Models\FAQs;
use Livewire\Component;

class Tutorial extends Component
{

    public $records;

    public function mount() {
        $this->records = FAQs::where('isDeleted', false)->get();
    }

    public function render()
    {
        return view('livewire.employee.tutorial');
    }
}
