<?php

namespace App\Livewire\Admin\Payroll;

use App\Http\Controllers\Admin\Services\OtherServices;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\GSISBilling;
use App\Models\Payroll;
use Carbon\Carbon;
use Livewire\Component;

class Process extends Component
{

    public $header;
    public $payroll_id;
    public $employee_no;
    public $employment_type;
    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        
        $payroll = Payroll::find($this->payroll_id);
        $employment_type = EmployementTypes::find($this->employment_type);
        if ($employment_type) {
            $employment_type = $employment_type->first();
        }

        if($this->employment_type) {

            if(!$payroll || !$employment_type) {
                return redirect()->route('payroll.index');
            }

            $this->employment_type = $employment_type->id;

            $this->payrollInfo($payroll, $employment_type);

        }
    }

    public function payrollInfo($payroll, $employment_type) {

        $employment_id = $employment_type->id;

        $employees = EmployeeInformation::with('section', 'personal', 'positions')
                            ->where('employment_type_id', $employment_id)
                            ->get();

        $other_service = new OtherServices;

        $data = [];

        # FOR PLANTILYA


        if($employment_id == 1) {

            foreach($employees as $employee) {

                $employee_no = $employee->employee_no;
            
                $employee_name = $employee->personal->firstname . ' ' . $employee->personal->lastname;
                $employee_position = $employee->positions->name;
                $employee_salary = $employee->monthly_rate;
    
                $deductions = $other_service->deductions($employee_no);
    
                $current_date = Carbon::parse($payroll->payroll_date)->format('m/Y');
                $employment_type = $employee->employment_type_id;
    
                $gsis_no = $employee->personal->gsis_no;
    
                $gsis_billing = GSISBilling::with(['items' => function ($query) use ($gsis_no) {
                            $query->where('crn_no', $gsis_no);
                        }])
                        ->where('billing_month', $current_date)
                        ->whereHas('items', function ($query) use ($gsis_no) {
                            $query->where('crn_no', $gsis_no);
                        })
                        ->first();

                $pera = collect($deductions)->firstWhere('code', 'PERA')['amount'] ?? 0;
                $gross_amount_earned = (float) $employee_salary + (float) $pera;
                $hmdf = collect($deductions)->firstWhere('deduction.code', 'HDMF')['amount'] ?? 0;
                $philhealth = collect($deductions)->firstWhere('deduction.code', 'PHILHEALTH')['amount'] ?? 0;
                $consoloan = $gsis_billing['items'][0]->consoloan ?? 0;
                $emergency_loan = $gsis_billing['items'][0]->emrgy_loan ?? 0;
                $plreg = $gsis_billing['items'][0]->plreg ?? 0;
                $mpl = $gsis_billing['items'][0]->mpl ?? 0;
                $cpl = $gsis_billing['items'][0]->cpl ?? 0;
                $mp2 = collect($deductions)->firstWhere('deduction.code', 'MP2')['amount'] ?? 0;
                $mplstlms = collect($deductions)->firstWhere('deduction.code', 'MPLSTLMS')['amount'] ?? 0;
                $cir = collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0;
                $allowance = collect($deductions)->firstWhere('deduction.code', 'allowance')['amount'] ?? 0;
    
                $total_deduction = 
                    (float) $hmdf + 
                    (float) $philhealth + 
                    (float) $consoloan + 
                    (float) $emergency_loan + 
                    (float) $plreg + 
                    (float) $mpl + 
                    (float) $cpl + 
                    (float) $mp2 + 
                    (float) $mplstlms + 
                    (float) $cir + 
                    (float) $allowance;
            
                $net_amount = (float) $gross_amount_earned - $total_deduction;

                $halfSalaryAmount = $net_amount / 2;

                $data[] = [
                    'employment_type' => $employment_type,
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'basic_salary' => number_format($employee_salary, 2),
                    'pera' => number_format($pera, 2),
                    'gross_amount_earned' => number_format($gross_amount_earned, 2),
                    'rlip' => 0,
                    'hdmf' => number_format($hmdf, 2),
                    'philhealth' => number_format($philhealth, 2),
                    'consoloan' => number_format($consoloan, 2),
                    'emergency_loan' => number_format($emergency_loan, 2),
                    'plreg' => number_format($plreg, 2),
                    'mpl' => number_format($mpl, 2),
                    'cpl' => number_format($cpl, 2),
                    'mp2' => number_format($mp2, 2),
                    'mplstlms' => number_format($mplstlms, 2),
                    'cir375_cir449' => number_format($cir, 2),
                    'w_tax' => 0,
                    'uca' => 0,
                    'allowance' => number_format($allowance, 2),
                    'aut' => 0,
                    'total_deductions' => number_format($total_deduction, 2),
                    'net_amount' => number_format($net_amount, 2),
                    'dbp_branch' => 0,
                    'kawani' => 0,
                    'lbp_payroll_account' => 0,
                    'first_half' => number_format($halfSalaryAmount, 2),
                    'second_half' => number_format($halfSalaryAmount, 2),
                ];
    
            }

            return $this->records = $data;
        }
        
        # FOR COS

        if($employment_id == 2) {
            
            foreach($employees as $employee) {

                $employee_no = $employee->employee_no;
            
                $employee_name = $employee->personal->firstname . ' ' . $employee->personal->lastname;
                $employee_position = $employee->positions->name;
                $employee_salary = $employee->monthly_rate;
    
                $deductions = $other_service->deductions($employee_no);
    
                $current_date = Carbon::parse($payroll->payroll_date)->format('m/Y');
                $employment_type = $employee->employment_type_id;
    
                $gsis_no = $employee->personal->gsis_no;
    
                $gsis_billing = GSISBilling::with(['items' => function ($query) use ($gsis_no) {
                            $query->where('crn_no', $gsis_no);
                        }])
                        ->where('billing_month', $current_date)
                        ->whereHas('items', function ($query) use ($gsis_no) {
                            $query->where('crn_no', $gsis_no);
                        })
                        ->first();

                $pera = collect($deductions)->firstWhere('code', 'PERA')['amount'] ?? 0;
                $gross_amount_earned = (float) $employee_salary + (float) $pera;
                $hmdf = collect($deductions)->firstWhere('deduction.code', 'HDMF')['amount'] ?? 0;
                $philhealth = collect($deductions)->firstWhere('deduction.code', 'PHILHEALTH')['amount'] ?? 0;
                $consoloan = $gsis_billing['items'][0]->consoloan ?? 0;
                $emergency_loan = $gsis_billing['items'][0]->emrgy_loan ?? 0;
                $plreg = $gsis_billing['items'][0]->plreg ?? 0;
                $mpl = $gsis_billing['items'][0]->mpl ?? 0;
                $cpl = $gsis_billing['items'][0]->cpl ?? 0;
                $mp2 = collect($deductions)->firstWhere('deduction.code', 'MP2')['amount'] ?? 0;
                $mplstlms = collect($deductions)->firstWhere('deduction.code', 'MPLSTLMS')['amount'] ?? 0;
                $cir = collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0;
                $allowance = collect($deductions)->firstWhere('deduction.code', 'allowance')['amount'] ?? 0;
    
                $total_deduction = 
                    (float) $hmdf + 
                    (float) $philhealth + 
                    (float) $consoloan + 
                    (float) $emergency_loan + 
                    (float) $plreg + 
                    (float) $mpl + 
                    (float) $cpl + 
                    (float) $mp2 + 
                    (float) $mplstlms + 
                    (float) $cir + 
                    (float) $allowance;
            
                $net_amount = (float) $gross_amount_earned - $total_deduction;

                $halfSalaryAmount = $net_amount / 2;

                $data[] = [
                    'employment_type' => $employment_type,
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'basic_salary' => number_format($employee_salary, 2),
                    'hdmf' => number_format($hmdf, 2),
                    'philhealth' => number_format($philhealth, 2),
                    'mp2' => number_format($mp2, 2),
                    'mplstlms' => number_format($mplstlms, 2),
                    'cir375_cir449' => number_format($cir, 2),
                    'uca' => 0,
                    'w_tax' => 0,
                    'aut' => 0,
                    'total_deductions' => number_format($total_deduction, 2),
                    'net_amount' => number_format($net_amount, 2),
                    'dbp_branch' => 0,
                    'kawani' => 0,
                    'lbp_payroll_account' => 0,
                    'first_half' => number_format($halfSalaryAmount, 2),
                    'second_half' => number_format($halfSalaryAmount, 2),
                ];
    
            }

            return $this->records = $data;

        }

    }

    public function save() {
        return redirect()
            ->route('payroll.process', ['payroll_id' => $this->payroll_id, 'employment_type' => $this->employment_type]);
    }

    public function render()
    {
        return view('livewire.admin.payroll.process');
    }
}
