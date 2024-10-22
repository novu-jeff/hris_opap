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
    protected $listeners = ['loadRecords'];

    
    public function mount() {

        # load user data

        $this->loadRecords();
        
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