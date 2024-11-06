<?php

namespace App\Livewire\Home\Modals;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{

    public $old_password;
    public $new_password;
    public $confirm_password;

    protected $listeners = ['change_password'];


    public function rules() {
        return [
            'old_password' => 'required',
            'new_password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required|min:8'
        ];
    }

    public function change_password(bool $isNotify = true) {

        $this->validate();

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to save password?',
                'message' => 'Please make sure to remember your password.',
                'action' => 'change_password'
            ]);
        }

        $record = Auth::guard('applicant')->user();
        $prev_password = $record->password;

        if(!Hash::check($this->old_password, $prev_password)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'isRemoveRowDT' => false,
                'message' => 'Old Password is incorrect.'
            ]);
        } 


        $new_password = Hash::make($this->new_password);
        $record->password = $new_password;
        $record->save();

        $this->reset();

        return $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Password Changed Successfully', 
            'message' => 'You\'re password has been changed.',
        ]);


    }

    public function render()
    {
        return view('livewire.home.modals.change-password');
    }
}
