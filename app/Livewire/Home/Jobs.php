<?php

namespace App\Livewire\Home;

use App\Models\JobApplicants;
use App\Models\JobPosts;
use App\Models\SavedJobs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class Jobs extends Component
{

    use WithPagination;

    public $user_id;
    public $record_info;
    public $applied_job_ids;
    public $saved_job_ids;
    public $search_query;
    public $search_term;
    public $search_result =  [];
    public $isEmptySearch = false;

    protected $paginationTheme = 'bootstrap';
    public $entries = 5;
    public $search = '';

    # load default data needed
    public function mount() {

        # initially store user id

        $this->user_id = Auth::guard('applicant')
            ->user()->id ?? null;

        # initially load all applied jobs

        $this->showAppliedJobs();

        # initially show if jobs are saved or not
        $this->showSavedJobs();

        # if parameter exists show filter

        if($this->search_query) {
            $this->find();
        }

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

    # apply logic
    public function apply(int $job_id) {

        if (!Auth::guard('applicant')->check()) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Account Required!', 
                'message' => 'You must create first an account before applying to any jobs. To register, you can visit <a href="'.route('home.register').'">here.</a>'
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
                'message' => 'You must create first an account before saving any jobs. To register, you can visit <a href="'.route('home.register').'">here.</a>'
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

    public function find() {

        $this->isEmptySearch = empty($this->search_query) ? true : false;

        $this->dispatch('navigateToSearch', $this->search_query);

        $this->search_term = $this->search_query;
    }

    # render view
    
    public function render()
    {

        $model = JobPosts::with('applicants', 'employment_type');

        if ($this->search_term) {

            $this->resetPage();

            $records = $model->where('position', 'like', '%' . $this->search_query . '%')
                ->orWhere('company_name', 'like', '%' . $this->search_query . '%')
                ->orWhere('location', 'like', '%' . $this->search_query . '%')
                ->orWhere('setup', 'like', '%' . $this->search_query . '%')
                ->orWhereHas('employment_type', function($query) {
                    $query->where('name', 'like', '%' . $this->search_query . '%');
                })
                ->orWhere('min_salary', 'like', '%' . $this->search_query . '%')
                ->orWhere('max_salary', 'like', '%' . $this->search_query . '%')
                ->orWhere('slots', 'like', '%' . $this->search_query . '%');

        }

        $records = $model->latest()->paginate($this->entries);

        if ($records->total() > 0) {
            $this->search_result['isEmpty'] = false;
        } else {
            $this->search_result['isEmpty'] = true;
            $this->isEmptySearch = false;
        }
        
        $this->search_result['parameter'] = $this->search_term;
        $this->search_result['total'] = $records->total() ?? 0;
        
        return view('livewire.home.jobs', [
            'records' => $records
        ]);
    }
}