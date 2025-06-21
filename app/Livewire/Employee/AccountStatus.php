<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeInformation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AccountStatus extends Component
{

    public function mount()
    {

        $user = Auth::user();

        $status = $user->information->status ?? null;

        if($status !== 'active') {
            return redirect()->route('employee.login')
                ->with(['error' => 'Oops, your account is currently inactive.']);
        }

    }

    public function render()
    {
        return <<<'HTML'
        <div>
            {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
        </div>
        HTML;
    }

}
