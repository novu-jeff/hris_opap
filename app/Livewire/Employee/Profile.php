<?php

namespace App\Livewire\Employee;
use Livewire\Component;

class Profile extends Component
{

    public $form;

    public function render()
    {
        return view('livewire.employee.profile');
    }
}
