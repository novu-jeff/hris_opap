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

        $previousUrl = url()->previous();
        $currentUrl = url()->current();
    
        // Check if the previous URL is the same as the current URL
        if ($previousUrl === $currentUrl) {
            // Check if the previous URL does not have '/job' in it
            if (!str_contains($previousUrl, '/job')) {
                return redirect('/'); // Redirect to home
            } else {
                return redirect('/job/applicants/pending'); // Redirect to pending applicants
            }
        }
    
        return redirect()->back(); // Default back redirect if the previous URL is different
    }
    
    
    public function render()
    {
        return view('livewire.home.view-job');
    }
}