<?php

namespace App\Livewire\Home\Modals;

use App\Livewire\Home\Profile;
use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UpdateProfile extends Component
{

    public $fields = [];
    public $user_id;
    public $activeTab = 'information';
    protected $listeners = ['save', 'loadRecords'];

    public function mount() {
        $id = Auth::guard('applicants')->user()->id;
        $record = ApplicantUsers::where('id', $id)
            ->first();
        $this->user_id = $id;
        $this->fields = [
            'information' => [
                'firstname' => $record->firstname ?? '',
                'middlename' => $record->middlename ?? '',
                'lastname' => $record->lastname ?? '',
                'phone_no' => $record->phone_no ?? '',
                'tel_no' => $record->tel_no ?? '',
                'sex' => $record->sex ?? '',
                'birthday' => $record->birthday ?? '',
                'civil_status' => $record->civil_status ?? '',
                'address' => $record->address ?? '',
                'province' => $record->province ?? '',
                'city' => $record->city ?? '',
            ],
            'education' => [
                'level' => $record->level ?? '',
                'school_name' => $record->school_name ?? '',
                'course' => $record->course ?? '',
                'started' => $record->started ?? '',
                'finished' => $record->finished ?? '',
            ],
        ];
        
    }

    public function rules() {
        return [
            'fields.information.firstname' => 'required',
            'fields.information.middlename' => 'nullable',
            'fields.information.lastname' => 'required',
            'fields.information.phone_no' => [
                'required',
                Rule::unique('applicant_users', 'phone_no')
                    ->ignore($this->user_id)
            ],
            'fields.information.tel_no' => 'nullable',
            'fields.information.sex' => 'required|in:male,female,not to say',
            'fields.information.birthday' => 'required',
            'fields.information.civil_status' => 'required|in:single,married,seperated',
            'fields.information.address' => 'required',
            'fields.information.province' => 'required',
            'fields.information.city' => 'required',
            'fields.education.level' => 'nullable|required_with:fields.education.school_name,fields.education.course,fields.education.started,fields.education.finished',
            'fields.education.school_name' => 'nullable|required_with:fields.education.level,fields.education.course,fields.education.started,fields.education.finished',
            'fields.education.course' => 'nullable|required_with:fields.education.level,fields.education.school_name,fields.education.started,fields.education.finished',
            'fields.education.started' => 'nullable|required_with:fields.education.level,fields.education.school_name,fields.education.course,fields.education.finished',
            'fields.education.finished' => 'nullable|required_with:fields.education.level,fields.education.school_name,fields.education.course,fields.education.started',

        ];
    }

    public function messages(){
        return [
            'fields.information.firstname.required' => 'The First Name is required.',
            'fields.information.lastname.required' => 'The Last Name is required.',
            'fields.information.phone_no.required' => 'The Mobile Number is required.',
            'fields.information.phone_no.unique' => 'The Mobile Number is already in use.',
            'fields.information.sex.required' => 'Please select your gender.',
            'fields.information.sex.in' => 'Invalid gender selected. Please choose Male, Female, or Prefer not to say.',
            'fields.information.birthday.required' => 'The Birthday is required.',
            'fields.information.civil_status.required' => 'Please select your civil status.',
            'fields.information.civil_status.in' => 'Invalid civil status selected. Please choose Single, Married, or Legally Separated.',
            'fields.information.address.required' => 'The Address is required.',
            'fields.information.province.required' => 'The Province is required.',
            'fields.information.city.required' => 'The City is required.',
            'fields.education.level.required_with' => 'The level field is required.',
            'fields.education.school_name.required_with' => 'The school name field is required.',
            'fields.education.course.required_with' => 'The course field is required.',
            'fields.education.started.required_with' => 'The started field is required.',
            'fields.education.finished.required_with' => 'The finished field is required.',
        ];
    }


    public function save(bool $isNotify = true) {

        $this->validate();

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to save your profile?',
                'message' => 'Please make sure that all the details in your profile are correct and current.',
                'action' => 'save'
            ]);
        }

        try {
                        
            $data = array_merge($this->fields['information'], $this->fields['education']);
            
            ApplicantUsers::where('id', $this->user_id)
                ->update($data);

            $this->dispatch('loadRecords')->to('home.profile');
            
            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Profile Updated!',
                'message' => 'You have successfully updated your profile.',
            ]);

        } catch (ValidationException $e) {
            $errors = $e->errors();
            $active = explode('.', array_key_first($errors))[1];
            $this->activeTab = $active;
            $this->setErrorBag($e->validator->errors());
        }
    }

    public function render()
    {
        return view('livewire.home.modals.update-profile');
    }
}