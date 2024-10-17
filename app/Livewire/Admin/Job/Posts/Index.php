<?php

namespace App\Livewire\Admin\Job\Posts;

use App\Models\JobPosts;
use Livewire\Component;

class Index extends Component
{

    public $records;

    public function mount() {
        $this->records = JobPosts::all();
    }

    public function render()
    {
        return view('livewire.admin.job.posts.index');
    }
}
