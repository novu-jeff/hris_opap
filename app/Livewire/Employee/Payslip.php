<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Payslip extends Component
{

    public $payroll;
    public $payslip;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $employee_no = Auth::user()->employee_no;
        $payrollService = new PayrollService;

        $payroll = Payroll::where('status', 'approved')
            ->orderBy('payroll_date', 'desc')
            ->first();

        $this->payroll = $payroll;

        $this->payslip = $payrollService->getData($payroll, $employee_no) ?? [];

    }

    public function changePeriod($control, $direction)
    {
        $currentDate = $this->payroll->payroll_date;
    
        $query = Payroll::where('status', 'approved');
    
        if ($direction == '-1') {
            // Previous payroll date
            $query->where('payroll_date', '<', $currentDate)->orderBy('payroll_date', 'desc');
        } else {
            // Next payroll date
            $query->where('payroll_date', '>', $currentDate)->orderBy('payroll_date', 'asc');
        }
    
        $nextPayroll = $query->first();
    
        if ($nextPayroll) {

            $this->payroll = $nextPayroll;
    
            $employee_no = Auth::user()->employee_no;
            $payrollService = new PayrollService;
            
            $payslip = $payrollService->getData($nextPayroll, $employee_no);
    
            $this->payslip = $payslip;
        }
    }
    
    

    public function render()
    {
        return view('livewire.employee.payslip');
    }
}
