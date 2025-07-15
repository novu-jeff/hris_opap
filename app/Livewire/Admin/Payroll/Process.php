<?php

namespace App\Livewire\Admin\Payroll;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\SocialSecurityBilling;
use App\Models\Payroll;
use App\Models\PayrollItems;
use Carbon\Carbon;
use Livewire\Component;


class Process extends Component
{
    public $payroll_id;
    public $type;

    public function render()
    {
        return view('livewire.admin.payroll.process');
    }
}
