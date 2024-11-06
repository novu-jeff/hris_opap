<?php

namespace App\Livewire\Home;

use App\Models\JobApplicants;
use App\Models\JobPosts;
use App\Models\SavedJobs;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Applied extends Component
{

    public $user_id;
    public $records;
    public $record_info;
    public $saved_jobs;
    public $applied_jobs_id;
    public $sort_status;

    public $listeners = ['withdraw'];

    public function mount() {

        # initially load user id
        $this->user_id = Auth::guard('applicant')->user()->id ?? null;

        # initially load all applied records

        $this->showRecords();

        # initially load if job is already applied or not
        
        $this->showAppliedJobs();

        $this->showSavedJobs();

    }

    # show all records
    public function showRecords() {

        $allowedSort = ['pending', 'reviewed', 'interview', 'placement', 'onboarding', 'hired', 'rejected'];

        $query = JobApplicants::with('job.applicants')
            ->where('user_id', $this->user_id);

        if (!empty($this->sort_status && in_array($this->sort_status, $allowedSort))) {
            $query->where('status', $this->sort_status);
        }

        $record_count = $query->count();

        if($record_count > 0) {
            $this->records = $query->latest()->get();
        } else {
            $this->records = null;
        }

        $this->record_info = null;
    }


    # show job status if apply or applied
    
    public function showAppliedJobs() {
        if(!Auth::guard('applicant')->check()) {
           return  $this->applied_jobs_id = [];
        }

        $this->applied_jobs_id = Auth::guard('applicant')->user()->applied->pluck('job_id')->toArray();
    }

    public function showSavedJobs() {
        // Check if the user is authenticated
        $user = Auth::guard('applicant')->user();
    
        if (!$user) {
            // If no authenticated user, return null
            return $this->saved_jobs = null;
        }
    
        // Retrieve saved jobs with eager loading
        $records = $user->saved_jobs()->with('job')->get();
    
        // Check if there are any saved jobs
        if ($records->isEmpty()) {
            return $this->saved_jobs = null;
        }
    
        // Set the saved jobs
        $this->saved_jobs = $records;
    }
    
 
    public function show_more($id) {
        try {
            $record = JobPosts::find($id);
            if(is_null($record)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!',
                    'message' => 'Unknown job selected'
                ]);
            }

            $this->record_info = $record;

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }
    }

    # trigger withdrawing of application
    public function withdraw($id, bool $withdraw = false) {

        if(!$withdraw) {
            $this->dispatch('notice', [
                'id' => $id,
                'title' => 'Withraw your application?',
                'message' => 'Please be informed that withdrawing your application will delete all of your progress and need to start over when applying again.'
            ]);
        } else {
            
            try {

                $record = JobApplicants::where('user_id', $this->user_id)
                    ->where('job_id', $id);

                if($record) {
                    $record->delete();
                    $this->dispatch('alert', [
                        'status' => 'success',
                        'title' => 'Application Withdrawed',
                        'message' => 'You have successfully withdrawed your application'
                    ]);
                    $this->showRecords();
                } else {
                    $this->dispatch('alert', [
                        'status' => 'error',
                        'title' => 'Oops',
                        'message' => 'Job application id `'.$id.'` does not exists'
                    ]);
                }

            } catch(\Exception $e) {
                $this->dispatch('alert', [
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Error occured: ' . $e->getMessage()
                ]);
            }

        }
    }

    # render view
    public function render()
    {
        return view('livewire.home.applied');
    }
}