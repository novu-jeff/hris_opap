<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeAnnouncements;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{

    public $announcements;

    public bool $isAllowedLeave;

    public function mount() {
        
        $this->loadRecords();

        $this->checkAllowed();

    }

    public function loadRecords() {
        $this->announcements = EmployeeAnnouncements::latest()->take(10)->get();
    }

    public function checkAllowed() {
        $product = config('app.product');

        if($product == 'opap') {
            if(Auth::user()->information->employment_type_id !== 1) {
                return $this->isAllowedLeave = false;
            }
            return $this->isAllowedLeave = true;
        }
    }

    public function render()
    {
        return view('livewire.employee.dashboard');
    }
}
