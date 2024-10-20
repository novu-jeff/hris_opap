<?php

namespace App\Livewire\Admin\Settings\Hris\BankInformation;

use App\Models\BankInformations;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = BankInformations::with('department_center')->get();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.bank-information.index');
    }
}
