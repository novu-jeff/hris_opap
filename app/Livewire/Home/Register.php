<?php

namespace App\Livewire\Home;

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
    public $fields = [];

    public function rules() {
        return [
            'fields.personal.firstname' => 'required',
            'fields.personal.lastname' => 'required',
            'fields.personal.phone_no' => 'required|unique:applicant_users,phone_no|regex:/^09\d{9}$/',
            'fields.personal.tel_no' => 'nullable|required_with:fields.personal.tel_no|regex:/\d{9}$/|unique:applicant_users,tel_no',
            'fields.personal.sex' => 'required|in:male,female,not to say',
            'fields.personal.birthday' => 'required|date|before:today -18 years',
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
            'fields.personal.firstname.required' => 'First name is required.',
            'fields.personal.lastname.required' => 'Last name is required.',
            'fields.personal.phone_no.required' => 'Mobile number is required.',
            'fields.personal.phone_no.numeric' => 'Mobile number must be a number.',
            'fields.personal.phone_no.unique' => 'Mobile number is already registered.',
            'fields.personal.phone_no.regex' => 'Phone number must be start with 09 and must have 11 digits.',
            'fields.personal.tel_no.required_with' => 'Telephone number is required.',
            'fields.personal.tel_no.regex' => 'Telephone number must follow the format 00 0000 0000.',
            'fields.personal.tel_no.unique' => 'Provided Telephone number already registered.',
            'fields.personal.sex.required' => 'Please select your gender.',
            'fields.personal.sex.in' => 'Please choose a valid option for gender.',
            'fields.personal.birthday.required' => 'Birth date is required.',
            'fields.personal.birthday.before' => 'Your age must be 18 years old and above.',
            'fields.personal.civil_status.required' => 'Please select your civil status.',
            'fields.personal.civil_status.in' => 'Please choose a valid option for civil status.',
            'fields.personal.address.required' => 'Address is required.',
            'fields.personal.province.required' => 'Province is required.',
            'fields.personal.city.required' => 'City is required.',
            'fields.account.resume.required' => 'Please upload your resume.',
            'fields.account.resume.mimes' => 'Resume must be a file of type: doc, docx, pdf.',
            'fields.account.email.required' => 'Email address is required.',
            'fields.account.email.email' => 'Email address must be a valid email.',
            'fields.account.email.unique' => 'Email address is already registered.',
            'fields.account.password.required' => 'Password is required.',
            'fields.account.password.min' => 'Password must be at least 8 characters long.',
            'fields.account.password.same' => 'Password and confirmation password must match.',
            'fields.account.confirm_password.required' => 'Confirmation password is required.',
            'fields.account.confirm_password.min' => 'Confirmation password must be at least 8 characters long.',
        ];
        
    }

  

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

                $folder = $user->firstname . '_' . $user->lastname . '_' . $user->id;

                $file->storeAs('users/applicant/' . $folder, $filename, 'public');
                
                DB::commit();

                $email = $this->fields['account']['email'];

                $this->fields = [];

                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'success',
                    'title' => 'Account Created!', 
                    'message' => 'User account `'.$email.'` has been created.',
                    'redirect' => route('home.login')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'message' => 'Error occured: ' . $e->getMessage(),
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

    public function render()
    {
        return view('livewire.home.register');
    }
}
