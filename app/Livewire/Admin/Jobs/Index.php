<?php

namespace App\Livewire\Admin\Jobs;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{

    public function mount() {
        
    }

    public function render()
    {
        return view('livewire.admin.jobs.index');
    }
}
