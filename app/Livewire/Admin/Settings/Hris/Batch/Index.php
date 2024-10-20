<?php

namespace App\Livewire\Admin\Settings\Hris\Batch;

use App\Models\BatchConfigurations;
use Livewire\Component;

class Index extends Component
{

    public object $records;

    public function mount() {
        $this->records = BatchConfigurations::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.batch.index');
    }
}
