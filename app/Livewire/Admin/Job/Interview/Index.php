<?php

namespace App\Livewire\Admin\Job\Interview;

use App\Models\Interview;
use Livewire\Component;

class Index extends Component
{

    public $records;

    public function mount(): void {
        $this->records = Interview::with('items')->get();
    }

    public function render()
    {
        return view('livewire.admin.job.interview.index');
    }
}
