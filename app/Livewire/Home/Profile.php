<?php

namespace App\Livewire\Home;

use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Profile extends Component
{

    use WithFileUploads;

    public object $record;
    public $resume;
    public $activeTab = 'profile';
    public $isUpdateProfile = false;
    protected $listeners = ['loadRecords'];

    public function showModal($modal) {
        $this->dispatch('showModal', [
            'modal' => $modal,
        ]);
    } 
    
    public function mount() {

        # load user data

        $this->loadRecords();
        
    }

    public function setActiveTab(string $tab) {
        $this->activeTab = $tab;
    }
    
    public function loadRecords() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::with(
            [
                    'skills.skills',
                    'applied.interview',
                ])->where('id', $id)->first();
        $this->record = $record;
    }
    
    public function render()
    {
        return view('livewire.home.profile');
    }
}