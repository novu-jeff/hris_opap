<?php

namespace App\Livewire\Admin\Payroll;

use Livewire\Component;


class Process extends Component
{
    public $payroll_id;
    public $type;

    public function render()
    {
        return view('livewire.admin.payroll.process');
    }
}
