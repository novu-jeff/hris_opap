<?php

namespace App\Livewire\Admin\Settings\Hris\Violation;

use App\Models\Violations;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = Violations::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.violation.index');
    }
}
