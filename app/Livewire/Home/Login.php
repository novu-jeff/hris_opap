<?php

namespace App\Livewire\Home;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{

    public $email;
    public $password;

    public function rules() {
        return [
            'email' => 'required|exists:applicant_users',
            'password' => 'required',
        ];
    }

    public function messages() {
        return [
            'email.exists' => 'The email provided does not exists.'
        ];
    }

    public function login() {
       
        $this->validate();

        if(Auth::guard('applicant')->attempt([
            'email' => $this->email,
            'password' => $this->password
        ])) {

            return redirect()->route('home.index');

        } else {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Invalid email or password',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.home.login');
    }
}
