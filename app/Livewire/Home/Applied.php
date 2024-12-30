<?php

namespace App\Livewire\Home;

use App\Models\JobApplicants;
use App\Models\JobPosts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Applied extends Component
{

    use WithPagination;

    public $user_id;
    public $record_info;
    public $saved_jobs;
    public $applied_jobs_id;
    public $sort_status;

    public $listeners = ['withdraw'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 5;
    public $search = '';

    public function mount() {

        # initially load user id
        $this->user_id = Auth::guard('applicant')->user()->id ?? null;

    }
    
    # show more info
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
                    $this->record_info = null;
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
    public function render() {
        
        $allowedSort = ['pending', 'reviewed', 'interview', 'placement', 'onboarding', 'hired', 'rejected'];

        $model = JobApplicants::with('job.applicants')
            ->where('user_id', $this->user_id);

        if (!empty($this->sort_status && in_array($this->sort_status, $allowedSort))) {

            $this->resetPage();

            $model->where('status', $this->sort_status);
        }

        $records = $model->latest()->paginate($this->entries);

        if($records->total() <= 0) {
            $this->record_info = null;
        }

        return view('livewire.home.applied', [
            'records' => $records
        ]);
    }
}