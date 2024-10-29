<?php

namespace App\Livewire\Home;

use App\Models\JobApplicants;
use App\Models\JobPosts;
use App\Models\SavedJobs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Jobs extends Component
{

    public $user_id;
    public $records;
    public $records_no = 1;
    public $record_info;
    public $applied_job_ids;
    public $saved_job_ids;
    public $search_query;
    public $search_result;
    protected $listeners = ['loadMoreRecords'];

    # load default data needed
    public function mount() {

        # initially store user id

        $this->user_id = Auth::guard('applicant')->user()->id ?? null;

        # initially load all job records

        if($this->search_query) {
            $this->search(true);
        } else {
            $this->showRecords();
        }   

        # initially load all applied jobs

        $this->showAppliedJobs();

        # initially show if jobs are saved or not
        $this->showSavedJobs();

    }

    # show records
    public function showRecords() {
        $this->records_no += 1;
        $record = JobPosts::with('applicants')
            ->take($this->records_no);

        if($record->count() > 0) {
            $this->records = $record->latest()->get();
        } else {
            $this->records = null;
        }
    }

    public function loadMoreRecords() {
        $this->records_no += 1;
        $this->showRecords();
    }

    # show job status if apply or applied
    public function showAppliedJobs() {
        if(!Auth::guard('applicant')->check()) {
           return  $this->applied_job_ids = [];
        }

        $this->applied_job_ids = Auth::guard('applicant')->user()->applied->pluck('job_id')->toArray();
    }

     # show job is already saved or not
     public function showSavedJobs() {
        if(!Auth::guard('applicant')->check()) {
           return  $this->saved_job_ids = [];
        }

        $this->saved_job_ids = Auth::guard('applicant')->user()->saved_jobs->pluck('job_id')->toArray();   
    }

    # view job's full info
    public function show_more($id) {
        try {
            $record = JobPosts::with('applicants')->find($id);
            if(is_null($record)) {
                $this->dispatch('alert', [
                    'status' => 'error',
                    'title' => 'Oops!',
                    'message' => 'Unknown job selected'
                ]);
            }

            $applicants_id = $record->applicants->pluck('user_id')->toArray(); 
            $record->applicant_ids = $applicants_id;

            $this->record_info = $record;

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }
    }

    # search logic
    public function search(bool $isSearched = false) {
        if(!$isSearched) {
            $this->dispatch('navigateToSearch', $this->search_query);
        } else {
            if(!empty($this->search_query)) {
                $records = JobPosts::with('applicants')->where('position', 'LIKE', '%' . $this->search_query . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $this->search_query . '%')
                    ->orWhere('setup', 'LIKE', '%' . $this->search_query . '%')
                    ->orWhere('location', 'LIKE', '%' . $this->search_query . '%')
                    ->orWhere('type', 'LIKE', '%' . $this->search_query . '%');

                    $this->search_result = [
                        'count' => $records->count(),
                        'is_empty_parameter' => false,
                        'parameter' => $this->search_query
                    ];

                    if($records->count() > 0) {
                        $this->records = $records->get();
                        $this->record_info = null;
                    } else {
                        $this->records = null;
                    }

            } else {
                $this->search_result = [
                    'count' => 0,
                    'is_empty_parameter' => true,
                    'parameter' => $this->search_query
                ];
                $this->records = JobPosts::latest()->get();
            }

        }
    }

    # apply logic
    public function apply(int $job_id) {

        if (!Auth::guard('applicant')->check()) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Account Required!', 
                'message' => 'You must create first an account before applying to our jobs. To register, you can visit <a href="'.route('register').'">here.</a>'
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

    # save this job
    public function save_job(int $id) {
        
        $jobPosts = JobPosts::class;
        $savedJobs = SavedJobs::class;

        $job_exists = $jobPosts::where('id', $id)->exists();
        $saved_already = $savedJobs::where('user_id', $this->user_id)
            ->where('job_id', $id)->exists();
        
        if (!Auth::guard('applicant')->check()) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Account Required!', 
                'message' => 'You must create first an account before applying to our jobs. To register, you can visit <a href="'.route('register').'">here.</a>'
            ]);
        }

        if($job_exists) {

            DB::beginTransaction();

            try {

                if(!$saved_already) {

                    $savedJobs::create([
                        'user_id' => $this->user_id,
                        'job_id' => $id
                    ]);

                    DB::commit();

                } else {
                    
                    $savedJobs::where('user_id', $this->user_id)
                        ->where('job_id', $id)
                        ->delete();
                    
                    DB::commit();

                }

                $this->showSavedJobs();

            } catch(\Exception $e) {

                DB::rollBack();

                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'message' => 'Error occured: ' . $e->getMessage()
                ]);
            }

        } else {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'The job does not exists!'
            ]);
        }

    }

    public function placeholder() {
        return view('livewire.home.placeholder.jobs');
    }

    # render view
    
    public function render()
    {
        return view('livewire.home.jobs');
    }
}