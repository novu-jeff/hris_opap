<?php

namespace App\Livewire\Home;

use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{

    public object $record;
    protected $listeners = ['loadRecords'];

    
    public function mount() {

        # load user data

        $this->loadRecords();
        
    }
    
    public function loadRecords() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::where('id', $id)->first();
        $this->record = $record;
    }
    
    public function test(){
        dd(123);
    }

    public function render()
    {
        return view('livewire.home.profile');
    }
}