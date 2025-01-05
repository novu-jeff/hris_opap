<?php

namespace App\Livewire\Home;

use App\Models\SavedJobs;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Saved extends Component
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

    public function render() {

        $model = SavedJobs::with('job')->where('user_id', $this->user_id);

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.home.saved', [
            'records' => $records
        ]);
    }
}
