<?php

namespace App\Livewire\Employee;

use App\Models\SalaryItemsPayroll;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Payslip extends Component
{

    public $employee_no;
    public $payroll;
    public $payslip;
    public $error;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $this->employee_no = Auth::user()->employee_no;

        $payroll = SalaryItemsPayroll::with('information.section', 'payroll')
            ->where('employee_no', $this->employee_no)
            ->whereHas('payroll', function($query) {
                return $query->where('status', 'approved');
            })
            ->first();

        if(!$payroll) {
            return $this->error = 'No Payslip Found';
        }

        $this->payroll = $payroll;
        $this->payslip = $payroll;

    }

    public function changePeriod($control, $direction) {
        
        $currentDate = $this->payroll->payroll->payroll_date ?? null;
        $employeeNo = $this->employee_no;

        if (!$currentDate) {
            return $this->error = 'Current payroll date not available';
        }

        $query = SalaryItemsPayroll::with('information.section', 'payroll')
            ->where('employee_no', $employeeNo)
            ->whereHas('payroll', function ($q) use ($currentDate, $direction) {
                $q->where('status', 'approved');

                if ($direction == '-1') {
                    $q->where('payroll_date', '<', $currentDate)->orderBy('payroll_date', 'desc');
                } else {
                    $q->where('payroll_date', '>', $currentDate)->orderBy('payroll_date', 'asc');
                }
            });

        $nextPayroll = $query->first();

        if ($nextPayroll) {
            $this->payroll = $nextPayroll;
            $this->payslip = $nextPayroll;
        } else {
            $this->error = 'No more payroll records in this direction.';
        }
    }

    public function render()
    {
        return view('livewire.employee.payslip');
    }
}
