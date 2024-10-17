<?php

namespace App\Livewire\Admin\Job\Requirements;

use App\Models\JobRequirements;
use Livewire\Component;

class Index extends Component
{
    public $records;

    public function mount(): void {
        $this->records = JobRequirements::all();
    }

    public function render()
    {
        return view('livewire.admin.job.requirements.index');
    }
}
