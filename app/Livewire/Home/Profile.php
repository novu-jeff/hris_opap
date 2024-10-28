<?php

namespace App\Livewire\Home;

use App\Models\ApplicantUsers;
use App\Models\JobApplicants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Profile extends Component
{

    use WithFileUploads;

    public $user_id;
    public $record;
    public $resume;
    public $activeTab = 'profile';
    public $isUpdateProfile = false;
    public $selected_id;
    protected $listeners = ['loadRecords'];

    public function showModal($modal) {
        $this->dispatch('showModal', [
            'modal' => $modal,
        ]);
    } 
    
    public function boot() {
        
        if(Session::has('target')) {
            $data = session('target');
            if(array_key_exists('page', $data) && $data['page'] == 'profile') {
                $this->setActiveTab($data['tab']);
            }

        }

        $this->loadRecords();

    }

    public function mount() {

        $this->loadRecords();
        
    }
    
    public function loadRecords() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::with(
            [
                    'skills.skills',
                    'applied.interview',
                    'applied.offer'
                ])->where('id', $id)->first();

        $this->user_id = $id;   
        $this->record = $record;

    }

    public function setActiveTab(string $tab) {
        $this->activeTab = $tab;

        $this->isSeen($tab);

        session()->forget('target');
    }

    public function isSeen(string $tab) {
        
        $record = JobApplicants::with('offer')->where('user_id', $this->user_id)->first();
    
        if (!$record) {
            return; 
        }
    
        if ($tab === 'interview' && $record->status === 'interview') {
            return $record->update([
                'isInterviewSeen' => true,
            ]);
        }
    
        if ($tab === 'placement' && $record->status === 'placement' && $record->offer != null) {
            return $record->update([
                'isPlacementSeen' => true,
            ]);
        }
    
        if ($tab === 'onboarding' && $record->status === 'onboarding') {
            return $record->update([
                'isOnboardingSeen' => true,
            ]);
        }
    }
    

    public function download_offer(int $id) {
        
        $record = JobApplicants::with('offer')
            ->where('id', $id)
            ->where('user_id', $this->user_id)
            ->first();

        if(is_null($record->offer)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Job offer does not exists'
            ]);
        }
        
        $path = 'public/applicant/users/'.$this->user_id. '/' . $record->job_id .'/offers/' . $record->offer->attachment;
        
        if(!Storage::exists($path)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Job offer does not exists'
            ]);
        } 

        return response()->download(Storage::path($path));

    }
    
    public function render()
    {
        return view('livewire.home.profile');
    }
}