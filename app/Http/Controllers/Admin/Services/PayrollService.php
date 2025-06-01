<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeave;
use App\Models\EmployeeTimelogs;
use App\Models\EmployementTypes;
use App\Models\GSISBilling;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService extends Controller {

    public function getData($payroll, $employee = null) {

        $employment_id = $payroll->employment_type;
        $payroll_date = $payroll->payroll_date;
        $cut_off_period = $payroll->cut_off_period;

        $employees = EmployeeInformation::with(['section', 'personal', 'positions'])
            ->where('employment_type_id', $employment_id)
            ->when($employee, function ($query) use ($employee) {
                $query->where('employee_no', $employee);
            })
            ->get();
    
        if($employees->isEmpty()) {
            throw new \Exception('No employees found for the specified employment type.');
        }

        $other_service = new OtherServices;
        $dtr_service = new TimeLogService;

        $data = [];
        
        # FOR PLANTILYA

        if($employment_id == 1) {

            foreach($employees as $employee) {

                $employee_no = $employee->employee_no;
            
                $employee_name = $employee->personal->firstname . ' ' . $employee->personal->lastname;
                $employee_position = $employee->positions->name;
                $employee_salary = $employee->monthly_rate;
                $employee_biometrics = $employee->bsd_no;
                
                $monthYear = Carbon::parse($employee->payroll_date)->format('m-Y');

                $dtr = $dtr_service->getDTRByRange($employee_biometrics, $monthYear, $cut_off_period);
    
                $earnings = $other_service->earnings($employee_no);
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

                $overtime = $this->convertMinsToMoney($employee_salary, $dtr['worked_days'], $dtr['overtime']);
                $aut = $this->convertMinsToMoney($employee_salary, $dtr['worked_days'], $dtr['aut']);
                
                $pera = collect($earnings)->firstWhere('code', 'PERA')['amount'] ?? 0;
                $gross_amount_earned = (float) $employee_salary + (float) $pera + (float) $overtime;
                
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
                $w_tax = $employee->positions->w_tax ?? 0;
                $uca = collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0;
                $allowance = collect($deductions)->firstWhere('deduction.code', 'allowance')['amount'] ?? 0;
                $dbp = collect($deductions)->firstWhere('deduction.code', 'DBP Savings')['amount'] ?? 0;
                $unlad_kawani = collect($deductions)->firstWhere('deduction.code', 'Unlad Kawani')['amount'] ?? 0;

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
                    (float) $w_tax +
                    (float) $allowance +
                    (float) $aut;
                    
            
                $net_amount = (float) $gross_amount_earned - $total_deduction;

                $halfSalaryAmount = $net_amount / 2;

                $data[] = [
                    'employee_no' => $employee_no,
                    'employment_type' => $employment_type,
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'basic_salary' => number_format($employee_salary, 2),
                    'pera' => number_format($pera, 2),
                    'overtime' => number_format($overtime, 2),
                    'gross_amount_earned' => number_format($gross_amount_earned, 2),
                    'rlip' => number_format($employee_salary * 0.09, 2),
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
                    'w_tax' => number_format($w_tax, 2),
                    'uca' => number_format($uca, 2),
                    'allowance' => number_format($allowance, 2),
                    'aut' => number_format($aut, 2),
                    'total_deductions' => number_format($total_deduction, 2),
                    'net_amount' => number_format($net_amount, 2),
                    'dbp' => number_format($dbp, 2),
                    'kawani' => number_format($unlad_kawani, 2),
                    'lbp_payroll_account' => number_format($net_amount, 2),
                    'salary' => number_format($halfSalaryAmount, 2),
                ];
    
            }
            
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

        }


        # FOR JOB ORDER

        # FOR TOTAL AMOUNTING

        $formatted_payroll_date = Carbon::parse($payroll_date)->format('F, d Y') ?? '';
        [$startPeriod, $endPeriod] = array_map(fn($d) => \Carbon\Carbon::parse($d)->format('M d, Y'), explode(' to ', $cut_off_period));
        $formatted_cutoff_period = "$startPeriod - $endPeriod";
        $employment_type = EmployementTypes::find($payroll->employment_type) ?? null;

        return [
            'employee' => [
                'name' => $employee->personal->firstname . ' ' . $employee->personal->lastname,
                'position' => $employee->positions->name . ' ' . '(' . $employee->positions->salary_grade . ')' ?? '',
                'unit' => $employee->section->name ?? '',
            ],
            'no_employees' => count($data) ?? 0,
            'employment_type' => $employment_type->name,
            'employment_type_id' => $employment_type->id ?? null,
            'payroll_date' => $formatted_payroll_date,
            'cutoff_period' => $formatted_cutoff_period ?? '',
            'payroll' => $data ?? [],
            'net_amount' => number_format(array_sum(array_map(fn($item) => (float) str_replace(',', '', $item['net_amount']), $data ?? [])), 2),
            'salary_amount' => number_format(array_sum(array_map(fn($item) => (float) str_replace(',', '', $item['salary']), $data ?? [])), 2),
        ];

    }

    private function convertMinsToMoney($salary, $workedDays, $minutesOT)
    {
        if ($workedDays <= 0) {
            return 0;
        }
    
        $amount = ($salary / $workedDays / 8 / 60) * $minutesOT;
    
        return $amount;
    }

}