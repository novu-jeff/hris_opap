<?php

namespace App\Livewire\Admin\Settings\Hris\Position;

use App\Models\Positions;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = Positions::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.position.index');
    }
}
