<?php

namespace App\Livewire\Home;

use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Profile extends Component
{

    public object $record;

    public function mount() {


        $this->data = new \stdClass();
        $this->record = new \stdClass();

        # load user data

        $this->getUserData();
        
    }
    
    public function getUserData() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::where('id', $id)->first();
        $this->record->user_id = $record->id;
        $this->record->image = $record->image;
        $this->record->firstname = $record->firstname;
        $this->record->middlename = $record->middlename;
        $this->record->lastname = $record->lastname;
        $this->record->phone_no = $record->phone_no;
        $this->record->tel_no = $record->tel_no;
        $this->record->sex = $record->sex;
        $this->record->birthday = $record->birthday;
        $this->record->civil_status = $record->civil_status;
        $this->record->address = $record->address;
        $this->record->province = $record->province;
        $this->record->city = $record->city;
        $this->record->level = $record->level;
        $this->record->school_name = $record->school_name;
        $this->record->course = $record->course;
        $this->record->started = $record->started;
        $this->record->finished = $record->finished;
        $this->record->email = $record->email;
        $this->record->resume = $record->resume;
        $this->record->date_joined = $record->created_at;
    }
    
    public function render()
    {
        return view('livewire.home.profile');
    }
}