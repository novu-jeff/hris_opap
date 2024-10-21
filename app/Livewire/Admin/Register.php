<?php

namespace App\Livewire\Admin;

use App\Models\ApplicantUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Register extends Component
{

    use WithFileUploads;

    public string $activeTab = 'personal';
    public array $fields = [];

    public function register() {
        
        try {

            $this->validate();

            $file = $this->fields['account']['resume'];
            $extension = $file->getClientOriginalExtension(); 
            $filename = 'applicant_resume_' . time() . '.' . $extension;

            DB::beginTransaction();
        
            try {
                
                $user = ApplicantUsers::create([
                    'firstname' => $this->fields['personal']['firstname'] ?? null,
                    'middlename' => $this->fields['personal']['middlename'] ?? null,
                    'lastname' => $this->fields['personal']['lastname'] ?? null,
                    'phone_no' => $this->fields['personal']['phone_no'] ?? null,
                    'tel_no' => $this->fields['personal']['tel_no'] ?? null,
                    'sex' => $this->fields['personal']['sex'] ?? null,
                    'birthday' => $this->fields['personal']['birthday'] ?? null,
                    'civil_status' => $this->fields['personal']['civil_status'] ?? null,
                    'address' => $this->fields['personal']['address'] ?? null,
                    'province' => $this->fields['personal']['province'] ?? null,
                    'city' => $this->fields['personal']['city'] ?? null,
                    'resume' => $filename,
                    'email' => $this->fields['account']['email'] ?? null,
                    'password' => Hash::make($this->fields['account']['password']) ?? null,
                ]);

                $file->storeAs('public/applicant/users/'.$user->id, $filename);
                
                DB::commit();

                $this->reset('fields');

                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'success',
                    'title' => 'Account Created!', 
                    'message' => 'User account `'.$this->fields['account']['email'].'` has been created.',
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'message' => 'Error occured: ' . $e->getMessage()
                ]);
            }

        } catch (ValidationException $e) {

            $errors = $e->validator->errors()->getMessages();
            $firstErrorKey = array_key_first($errors);
            $keys = explode('.', $firstErrorKey);
            if (isset($keys[1])) {
                $this->activeTab = $keys[1];
            }

            throw $e; 
    
        }

        
    }

    public function rules() {
        return [
            'fields.personal.firstname' => 'required',
            'fields.personal.lastname' => 'required',
            'fields.personal.phone_no' => 'required|numeric|unique:applicant_users,phone_no',
            'fields.personal.sex' => 'required|in:male,female,not to say',
            'fields.personal.birthday' => 'required',
            'fields.personal.civil_status' => 'required|in:single,married,seperated',
            'fields.personal.address' => 'required',
            'fields.personal.province' => 'required',
            'fields.personal.city' => 'required',
            'fields.account.resume' => 'required|mimes:doc,docx,pdf',
            'fields.account.email' => 'required|email|unique:applicant_users,email',
            'fields.account.password' => 'required|min:8|same:fields.account.confirm_password',
            'fields.account.confirm_password' => 'required|min:8'
        ];
    }

    public function messages() {
        return [
            'fields.personal.firstname.required' => 'The first name is required.',
            'fields.personal.lastname.required' => 'The last name is required.',
            'fields.personal.phone_no.required' => 'The mobile number is required.',
            'fields.personal.phone_no.numeric' => 'The mobile number must be a number.',
            'fields.personal.phone_no.unique' => 'The mobile number is already registered.',
            'fields.personal.sex.required' => 'Please select your gender.',
            'fields.personal.sex.in' => 'Please choose a valid option for gender.',
            'fields.personal.birthday.required' => 'The birth date is required.',
            'fields.personal.civil_status.required' => 'Please select your civil status.',
            'fields.personal.civil_status.in' => 'Please choose a valid option for civil status.',
            'fields.personal.address.required' => 'The address is required.',
            'fields.personal.province.required' => 'The province is required.',
            'fields.personal.city.required' => 'The city is required.',
            'fields.account.resume.required' => 'Please upload your resume.',
            'fields.account.resume.mimes' => 'The resume must be a file of type: doc, docx, pdf.',
            'fields.account.email.required' => 'The email address is required.',
            'fields.account.email.email' => 'The email address must be a valid email.',
            'fields.account.email.unique' => 'The email address is already registered.',
            'fields.account.password.required' => 'The password is required.',
            'fields.account.password.min' => 'The password must be at least 8 characters long.',
            'fields.account.password.same' => 'The password and confirmation password must match.',
            'fields.account.confirm_password.required' => 'The confirmation password is required.',
            'fields.account.confirm_password.min' => 'The confirmation password must be at least 8 characters long.',
        ];
        
    }

    public function render()
    {
        return view('livewire.admin.register');
    }
}
