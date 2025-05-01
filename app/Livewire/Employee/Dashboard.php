<?php

namespace App\Livewire\Employee;

use App\Models\CompanyInformation;
use App\Models\EmployeeAnnouncements;
use App\Models\EmployeeAtro;
use App\Models\EmployeeBusinessSlip;
use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveCard;
use App\Models\EmployeeRequestLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{

    public $announcements;
    public $companyInfo;
    public $applications;

    public bool $isForRCOnly;

    public function mount() {
        
        $this->loadRecords();
        $this->checkAllowed();
    }

    public function loadRecords() {

        $employee_no = Auth::user()->employee_no;

        $this->announcements = EmployeeAnnouncements::latest()->take(10)->get();
        $this->companyInfo = $this->getCompanyInformation();
        $this->applications = [
            'leave' => [
                'title' => 'Leave Application',
                'count' => EmployeeLeave::where('employee_no', $employee_no)
                    ->where('status', 'pending')
                    ->count(),
                'route' => 'employee.leave',
            ],
            'atro' => [
                'title' => 'ATRO Application',
                'count' => EmployeeAtro::where('employee_no', $employee_no)
                    ->where('status', 'pending')
                    ->count(),
                'route' => 'employee.atro',
            ],
            'request_log' => [
                'title' => 'Request TimeLog',
                'count' => EmployeeRequestLog::where('employee_no', $employee_no)
                    ->where('status', 'pending')
                    ->count(),
                'route' => 'employee.request-timelog',
            ],
            'oba' => [
                'title' => 'OB Application',
                'count' => EmployeeBusinessSlip::where('employee_no', $employee_no)
                    ->where('status', 'pending')
                    ->count(),
                'route' => 'employee.obs.index',
            ]
        ];
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
