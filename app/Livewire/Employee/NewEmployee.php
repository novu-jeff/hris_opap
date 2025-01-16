<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class NewEmployee extends Component
{

    public $employee_no;
    public $name;
    public bool $isNewEmployee;
    public $password;
    public $confirm_password;

    public $listeners = ['not'];

    public function mount() {
        $user = Auth::user();
        $this->isNewEmployee = $user->isNew;
        $this->employee_no = $user->employee_no;
        $this->name = $user->load('personal')->personal->firstname . ' ' . $user->personal->lastname;        
    }

    protected function rules() {
        return [
            'password' => 'required|min:8|same:confirm_password',
        ];
    }

    public function save() {

        $this->validate();

        try {

            EmployeeAccount::where('employee_no', $this->employee_no)->update([
                'password' => Hash::make($this->password),
                'isNew' => false
            ]);

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Yey!', 
                'message' => 'Your account password has been updated successfully. You can now access your dashboard.',
                'redirect' => route('employee.dashboard')
            ]);

        } catch (\Exception $e) {
           return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }



    }

    public function render()
    {
        return view('livewire.employee.new-employee');
    }
}
