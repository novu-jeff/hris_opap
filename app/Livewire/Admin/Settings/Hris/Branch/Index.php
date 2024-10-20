<?php

namespace App\Livewire\Admin\Settings\Hris\Branch;

use App\Models\Branches;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = Branches::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.branch.index');
    }
}
