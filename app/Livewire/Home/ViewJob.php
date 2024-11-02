<?php

namespace App\Livewire\Home;

use App\Models\JobApplicants;
use App\Models\JobPosted;
use App\Models\JobPosts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ViewJob extends Component
{

    public $user_id;
    public $slug;
    public $record;
    public $applied_jobs_id;
    public $search_query;

    # load default data needed
    public function mount($slug) {

        # initially store user id

        $this->user_id = Auth::guard('applicant')->user()->id ?? null;

        # initially load the slug

        $this->slug = $slug;

        # initially load all job records
        
        $this->showRecords();

        # initially load all applied jobs

        $this->showAppliedJobs();

    }

    # apply logic
    public function apply(int $job_id) {

        if (!Auth::guard('applicant')->check()) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Account Required!', 
                'message' => 'You must create first an account before applying to our jobs. To register, you can visit <a href="'.route('home.register').'">here.</a>'
            ]);
        }

        $rules = [
            'job_id' => 'required|exists:job_posts,id',
            'user_id' => 'required|exists:applicant_users,id'
        ];

        $validator = Validator::make([
            'job_id' => $job_id,
            'user_id' => $this->user_id
        ], $rules);

        if($validator->fails()) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Error occured: ' . $validator->errors()
            ]);
        }

        $record = JobApplicants::where('user_id', $this->user_id)
            ->where('job_id', $job_id)
            ->exists();

        if($record) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Already Applied!', 
                'message' => 'You have already applied to this job, please wait for the employer\'s response.'
            ]);
        }

        DB::beginTransaction();

        try {

            # insert application to db 

            JobApplicants::create([
                'user_id' => $this->user_id,
                'applicant_no' => generate_code('APP'),
                'job_id' => $job_id,
                'status' => 'pending'
            ]);
            
            DB::commit();

            # update the status of apply button when usue applied

            $this->showAppliedJobs();

            # dispatch event if user successfully applied

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Congratulations!', 
                'message' => 'Your application has been sent to the employer. Please wait for further instructions.'
            ]);

            $this->dispatch('sample');

        } catch (\Exception $e) {
            
            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }

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
        if(!Auth::guard('applicant')->check()) {
           return  $this->applied_jobs_id = [];
        }

        $this->applied_jobs_id = Auth::guard('applicant')->user()->applied->pluck('job_id')->toArray();
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