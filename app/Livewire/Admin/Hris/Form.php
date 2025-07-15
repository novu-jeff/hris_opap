<?php

namespace App\Livewire\Admin\Hris;

use Livewire\Component;

class Form extends Component
{

    public $employee_no;
    public $form;
    public $tabsHasChanges = [];

    public function render() {
        return view('livewire.admin.hris.form');
    }
}
