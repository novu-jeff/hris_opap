<?php

namespace App\Livewire\Home;

use App\Models\JobPosted;
use App\Models\JobPosts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ViewJob extends Component
{

    public $slug;
    public $record;
    public $applied_jobs_id;
    public $search_query;

    # load default data needed
    public function mount($slug) {

        # initially load the slug

        $this->slug = $slug;

        # initially load all job records
        
        $this->showRecords();

        # initially load all applied jobs

        $this->showAppliedJobs();

    }

    # show records

    public function showRecords() {
        
        $record = JobPosts::where('slug', $this->slug)
            ->latest();
        
        if($record->exists()) {
            return $this->record = $record->first();
        }
        
        return null;
    }

    # show job status if apply or applied
    public function showAppliedJobs() {
        if(!Auth::guard('applicants')->check()) {
           return  $this->applied_jobs_id = [];
        }

        $this->applied_jobs_id = Auth::guard('applicants')->user()->applied->pluck('job_id')->toArray();
    }

    public function go_back() {
        return redirect()->back();
    }
    
    public function render()
    {
        return view('livewire.home.view-job');
    }
}