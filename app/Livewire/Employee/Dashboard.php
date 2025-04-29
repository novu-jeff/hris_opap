<?php

namespace App\Livewire\Employee;

use App\Models\CompanyInformation;
use App\Models\EmployeeAnnouncements;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{

    public $announcements;
    public $companyInfo;

    public bool $isForRCOnly;

    public function mount() {
        
        $this->loadRecords();

        $this->checkAllowed();

        $this->companyInfo = $this->getCompanyInformation();

    }

    public function loadRecords() {
        $this->announcements = EmployeeAnnouncements::latest()->take(10)->get();
        $this->companyInfo = $this->getCompanyInformation();
    }

    public function checkAllowed() {
        $product = config('app.product');

        if($product == 'government') {
            if(Auth::user()->information->employment_type_id !== 1) {
                return $this->isForRCOnly = false;
            }
            return $this->isForRCOnly = true;
        }
    }

    private function getCompanyInformation() {
        $companyInfo = CompanyInformation::with('type')->first();
        return $companyInfo;
    }

    public function render()
    {
        return view('livewire.employee.dashboard');
    }
}
