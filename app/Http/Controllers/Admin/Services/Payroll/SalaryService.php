<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Jobs\PayrollJob;
use App\Models\EmployementTypes;
use App\Models\SalaryPayroll;
use App\Services\ContributionsService;
use App\Services\SummaryServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\DailyTimeRecordService;
use App\Models\Loan;
use App\Models\Positions;
use App\Models\Tranche;
use Illuminate\Support\Facades\Log;

class SalaryService extends Controller {

    protected $product;
    protected $bsd_emp_identical;
    protected $payrollService;

    public function __construct(PayrollService $payrollService) {
        $this->product = config('app.product');
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->payrollService = $payrollService;
    }

    public function getPayroll(int $payroll_id) {

        $payroll = SalaryPayroll::with('items.information.section', 'employment_type')->findOrFail($payroll_id);

        $payroll->formatted_payroll_date = Carbon::parse($payroll->payroll_date)->format('F d, Y');

        if(!is_null($payroll->cut_off_period)) {
            [$startPeriod, $endPeriod] = explode(' to ', $payroll->cut_off_period);
            $payroll->formatted_cutoff_period = sprintf(
                '%s - %s',
                Carbon::parse(trim($startPeriod))->format('M d, Y'),
                Carbon::parse(trim($endPeriod))->format('M d, Y')
            );
        } else {
            $payroll->formatted_cutoff_period = null;
        }

        $employmentType = EmployementTypes::find($payroll->employment_type);
        $payroll->formatted_employment_type = $employmentType->name ?? '';

        $payroll->no_employees = $payroll->items
            ->pluck('employee_no')
            ->unique()
            ->count();

        $isFirstHalf = false;
        if ($payroll->cut_off_period) {
            [$startDate] = explode(' to ', $payroll->cut_off_period);
            $isFirstHalf = Carbon::parse(trim($startDate))->day <= 15;
        }

        $overallNetAmount = 0;
        $overallSalary = 0;

        foreach ($payroll->items as $item) {
            $amount = $isFirstHalf
                ? (float) $item->net_first_half
                : (float) $item->net_second_half;

            $overallSalary += $amount;
            $overallNetAmount += $amount;
        }


        $netAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));
        //$salaryAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->salary));

        $payroll->overall_net_amount = round($netAmount, 2);
       // $payroll->overall_salary = round($salaryAmount, 2);
       // $payroll->type = 'Salary Payroll';

        $payroll->overall_salary = round($overallSalary, 2);
      //  $payroll->overall_net_amount = round($overallNetAmount, 2);
        $payroll->type = 'Salary Payroll';

        $grouped = [];

        foreach ($payroll->items as $item) {
            $section = $item->information->section ?? null;

            if (!$section) continue;

            $sectionId = $section->id;
            $sectionName = $section->name;

            if (!isset($grouped[$sectionId])) {
                $grouped[$sectionId] = [
                    'section_id' => $sectionId,
                    'section_name' => $sectionName,
                    'employees' => [],
                ];
            }

            $grouped[$sectionId]['employees'][] = $item->toArray() ?? [];
        }

        $items = array_values($grouped);

        return [
            'payroll' => $payroll->toArray() ?? [],
            'payroll_items' => $items,
            'batch_id' => $payroll->batch_id ?? null,
        ];
    
    }

    public function rules(array $payload)
    {
        return [
            'payroll_date' => [
                'required',
                'date',
            ],
            'cut_off_period' => [
                'required',
                'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/',
            ],
            'employment_type' => 'exists:employment_types,id',
            'has_deductions' => 'nullable|boolean'
        ];
    }




    public function createPayroll($payload) {
        $payroll = SalaryPayroll::create([
            'payroll_date' => $payload['payroll_date'],
            'cut_off_period' => $payload['cut_off_period'],
            'employment_type' => $payload['employment_type'],
            'hasDeductions' => $payload['has_deductions'] ?? false,
            'status' => 'pending'
        ]);

        return $payroll;

    }

    public function generateChunks(int $payroll_id, int $employment_type, string $type) {

        $payroll = SalaryPayroll::findOrFail($payroll_id);

        if(!$payroll) {
            return [
                'status' => 'error',
                'message' => 'Error occured: Unknown payroll_id ' . $payroll_id
            ];
        }

        $employees = $this->payrollService->getEmployees($employment_type, $type);
        $employees = $employees['eligible']['items'];

        $chunks = array_chunk($employees, 1000);

        $jobs = [];

       // dd('hre');

        foreach ($chunks as $chunk) {
           // dd('set');
            $jobs[] = new PayrollJob(
                                    $chunk,          // already an array
                                    $payroll->id,    // pass only ID
                                    'salary'
                                );
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M d, Y');

        if(!empty($jobs)) {
            return [
                'status' => 'success',
                'jobs' => $jobs,
                'name' => 'Payroll For ' . $payroll_date,
                'payroll' => $payroll
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'No jobs were processed'
            ];
        }

    }

    private function getFirstHalfNetAmount(
        string $employeeNo,
        SalaryPayroll $currentPayroll
    ): float {
        return SalaryPayroll::query()
            ->whereMonth('payroll_date', Carbon::parse($currentPayroll->payroll_date)->month)
            ->whereYear('payroll_date', Carbon::parse($currentPayroll->payroll_date)->year)
            ->where('cut_off_period', 'like', '%01%15%')
            ->where('status', 'approved')
            ->whereHas('items', function ($q) use ($employeeNo) {
                $q->where('employee_no', $employeeNo);
            })
            ->with(['items' => function ($q) use ($employeeNo) {
                $q->where('employee_no', $employeeNo);
            }])
            ->get()
            ->pluck('items')
            ->flatten()
            ->first()
            ->net_first_half ?? 0;
    }


    public function computePayroll($payroll, $employees, $type) {

        $hasDeductions = $payroll->hasDeductions ?? false;

     


        if ($this->product == 'government') {

            $cutoff = $payroll->cut_off_period;

            $isFirstHalf = false;

            if ($cutoff) {
                [$startDate, $endDate] = explode(' to ', $cutoff);

                $startDay = (int) Carbon::parse($startDate)->day;

                $isFirstHalf = $startDay <= 15;
            }


            $other_service = new OtherServices;
            $dtr_service = new DailyTimeRecordService;
            $leaveCard_service = new LeaveCardService;
            $payroll_service = app(PayrollService::class);

            $data = [];

            foreach ($employees as $employee) {
                $employee_no = $employee['employee_no'];
                $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $position = $employee['position_name'];
                $position_id = $employee['position_id'];
                $eligible = $employee['employment_type_id'];
                //$basic_salary = round(floatval($employee['salary']), 2);
                $salary_type = $employee['salary_type'];
               // $gw_tax = $employee['w_tax'];
                $rate = 0.05;
                $ceiling = 100000;
                $stepId = $employee['step_id'];

              //  dd($employee['employment_type_id']);

                if ($employee['employment_type_id'] != 3 && $employee['employment_type_id'] != 4) {


               $salaryGrade = Positions::where('id', $position_id)->value('salary_grade');

               $stepColumn = "step_" . ($employee['step_id'] ?? '');
                $stepColumnTax = "step_" . ($employee['step_id'] ?? '') . "_wtax";

                // Get the latest tranche for this eligible type
                $latestTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn, $stepColumnTax) {
                    $query->where('salary_grade', $salaryGrade)
                     ->select('id', 'tranche_id', 'salary_grade', $stepColumn, $stepColumnTax);
                }])
                 ->where('eligible', $eligible)
                ->where('is_active', 1)
                ->latest('year')
                ->first();
                

                $trancheItem = $latestTranche?->items?->first();

                if (!$trancheItem) {
                    logger()->warning('Missing active tranche item for payroll computation', [
                        'payroll_id' => $payroll->id,
                        'employee_no' => $employee_no,
                        'position_id' => $position_id,
                        'salary_grade' => $salaryGrade,
                        'eligible' => $eligible,
                        'step_id' => $employee['step_id'] ?? null,
                    ]);
                }

                $salary = $trancheItem ? data_get($trancheItem, $stepColumn, 0) : 0;
                $wtax = $trancheItem ? data_get($trancheItem, $stepColumnTax, 0) : 0;

                } else {
                   // dd('here');
                    $wtax = data_get($employee, 'w_tax', 0);
                    $salary = data_get($employee, 'salary', 0);
                }  
                
               // dd($salary, $wtax);

                $basic_salary = round(floatval($salary), 2);
                $salary_type = $employee['salary_type'];
                $gw_tax = $wtax;

                [$startDate, $endDate] = explode(' to ', $payroll->cut_off_period);
                $cutoffEndDate = Carbon::parse(trim($endDate))->toDateString(); 

                $monthYear = Carbon::parse($payroll->payroll_date)->format('m-Y');
                $cut_off_period = $other_service->splitDateRange($payroll->cut_off_period);

                $dtr = $dtr_service->getDailyTimeRecord($employee_no, $cut_off_period, true);

                $dtr_summary  = $dtr['summary'];

                $overtimeData = $payroll_service->computeOvertimePay($basic_salary, $dtr_summary['worked_days'], $dtr_summary['overtime_minues']);
                
                $overtime = $overtimeData['gross_ot_pay'];

                $earnings = $other_service->earnings($employee_no);
                $deductions = $hasDeductions ? $other_service->deductions($employee_no, $cutoffEndDate) : [];

                //$current_date = Carbon::parse($payroll->payroll_date)->format('m/Y');
                $current_date = Carbon::parse($payroll->payroll_date)->format('Y-m-d');

                $social_security = $hasDeductions
                    ? DB::table('social_security as gb')
                        ->join('social_security_items as gi', 'gb.id', '=', 'gi.social_security_id')
                        ->where('gb.billing_month', $current_date)
                        ->where('gi.bp_no', $employee['bp_no'])
                        ->select('gi.consoloan', 'gi.emrgy_loan', 'gi.plreg', 'gi.mpl', 'gi.mpl_lite', 'gi.cpl')
                        ->first() ?? (object) []
                    : (object) [];
               

                // Earnings
                $pera = round(floatval(collect($earnings)->firstWhere('code', 'PERA')['amount'] ?? 0), 2);
                $gross = round($basic_salary + $pera, 2);

              //  dd($hasDeductions, $social_security->consoloan );
                if($eligible !== 2 && $eligible !== 3 && $eligible !== 4) {
                    // DEDUCTION FOR GOVERNMENT EMPLOYEES
                    //dd('here');
                    $rlip = $hasDeductions ? round(floatval($basic_salary * 0.09), 2) : 0;
                }else{
                    $rlip =  0;
                }
                // Deductions (based on flag)
                
               // $philhealth = $hasDeductions ? round(floatval($basic_salary * 0.05 / 2), 2) : 0;
               $philhealth = $hasDeductions
                    ? round(min($basic_salary, $ceiling) * $rate / 2, 2)
                    : 0;
                $hdmf = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'HDMF')['amount'] ?? 0), 2) : 0;
                $mp2 = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'MP2')['amount'] ?? 0), 2) : 0;
                $mplstlms = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'MPLSTLMS')['amount'] ?? 0), 2) : 0;
                $cir = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'CIR')['amount'] ?? 0), 2) : 0;
               // $w_tax = $hasDeductions ? round(floatval($payroll_service->computeWithholdingTax($basic_salary) ?? 0), 2) : 0;
                $w_tax = $hasDeductions ? round(floatval($gw_tax ?? 0), 2) : 0;
               $uca = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'Unliquidated_Cash_Advances')['amount'] ?? 0), 2) : 0;
                $consoloan = $hasDeductions ? round(floatval($social_security->consoloan ?? 0), 2) : 0;
                $emergency_loan = $hasDeductions ? round(floatval($social_security->emrgy_loan ?? 0), 2) : 0;
                $plreg = $hasDeductions ? round(floatval($social_security->plreg ?? 0), 2) : 0;
                $mpl = $hasDeductions ? round(floatval($social_security->mpl ?? 0), 2) : 0;
                $mpl_lite = $hasDeductions ? round(floatval($social_security->mpl_lite ?? 0), 2) : 0;
                $cpl = $hasDeductions ? round(floatval($social_security->cpl ?? 0), 2) : 0;

                if ($employee['employment_type_id'] != 1){
                    $aut = $hasDeductions ? round(floatval($payroll_service->computeAutDeduction($dtr_summary, $basic_salary, $salary_type))) : 0;
                }else{
                    $aut = 0;
                }
                // Optional deductions
               // $dbp = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'DBP')['amount'] ?? 0), 2) : 0;
               // $kawani = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'Kawani')['amount'] ?? 0), 2) : 0;

                $dbp = 0;
                $kawani = 0;

                if ($hasDeductions) {
                    $filteredDeductions = collect($deductions)->filter(function ($item) use ($cutoffEndDate) {
                        return empty($item['valid_until']) || $item['valid_until'] >= $cutoffEndDate;
                    });

                    $dbp = round(floatval($filteredDeductions->firstWhere('code', 'DBP')['amount'] ?? 0), 2);
                    $kawani = round(floatval($filteredDeductions->firstWhere('code', 'Kawani')['amount'] ?? 0), 2);
                }
                
                $total_deduction = $rlip + $hdmf + $philhealth + $consoloan + $emergency_loan +
                    $plreg + $mpl + $mpl_lite + $cpl + $mp2 + $mplstlms + $cir + $w_tax + $uca + $aut;

                $total_lbp =  $dbp +  $kawani;  


                $net = round($gross - $total_deduction, 2);

                if(!empty($total_lbp)){
                    $lbp = $net - $total_lbp;
                }else{
                    $lbp = $net;
                }
                $half = round($net / 2, 2);
                if ($isFirstHalf) {
                  //  dd($lbp);
                    // FIRST HALF PAYROLL (01–15)
                    if(!empty($total_lbp)){
                       // dd($net);
                        $firstHalf  = floor(($net  / 2) * 100) / 100;
                       // dd($firstHalf);
                        $firstHalf =  $firstHalf - $total_lbp;
                        $secondHalf = round($lbp - $firstHalf, 2);
                        
                    }else{
                        $firstHalf  = floor(($net  / 2) * 100) / 100;
                        $secondHalf = round($net - $firstHalf, 2);
                    }
                   

                } else {

                    // SECOND HALF PAYROLL (16–end)
                    $firstHalf = $this->getFirstHalfNetAmount($employee_no, $payroll);

                    $secondHalf = round($lbp - $firstHalf, 2);
                }
              //  $firstHalf  = floor(($net / 2) * 100) / 100;
              //  $secondHalf = round($net - $firstHalf, 2);

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'name' => $name,
                    'position' => $position,
                    'basic_salary' => $basic_salary,
                    'pera' => $pera,
                    'gross_amount_earned' => $gross,
                    'overtime_pay' => $overtime,
                    'rlip' => $rlip,
                    'hdmf' => $hdmf,
                    'philhealth' => $philhealth,
                    'consoloan' => $consoloan,
                    'emergency_loan' => $emergency_loan,
                    'plreg' => $plreg,
                    'mpl' => $mpl,
                    'mpl_lite' => $mpl_lite,
                    'cpl' => $cpl,
                    'mp2' => $mp2,
                    'mplstlms' => $mplstlms,
                    'cir375_cir449' => $cir,
                    'w_tax' => $w_tax,
                    'uca' => $uca,
                    'aut' => $aut,
                    'total_deductions' => $total_deduction,
                    'net_amount' => $net,
                    'dbp' => $dbp,
                    'kawani' => $kawani,
                    'lbp_payroll_account' => $lbp,
                    'salary' => $half,
                    'net_first_half' => $firstHalf,
                    'net_second_half' => $secondHalf,
                    'is_first_half_locked' => $isFirstHalf ? 0 : 1,
                    'is_second_half_locked' => $isFirstHalf ? 1 : 0,
                ];
            }

            return $data;

        } else {

            $other_service = new OtherServices;
            $dtr_service = new DailyTimeRecordService;
            $leaveCard_service = new LeaveCardService;
            $contribution_service = new ContributionsService;
            $payroll_service = app(PayrollService::class);

            $data = [];

            foreach ($employees as $employee) {
                $employee_no = $employee['employee_no'];
                $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $position = $employee['position_name'];
                $basic_salary = round(floatval($employee['salary']), 2);


                 // Fetch approved loans for this employee
                $employeeLoans = Loan::where('employee_no', $employee_no)
                    ->where('status', 'approved')
                    ->get();

                $loanDeductionsToInsert = [];
                $other_loans = 0;

                foreach ($employeeLoans as $loan) {
                    if ($loan->balance > 0) {
                        $deductionAmount = $loan->monthly_amortization;
                        $other_loans += $deductionAmount;

                        $loanDeductionsToInsert[] = [
                            'payroll_item_id' => 0, // updated later
                            'reference_type' => 'loan',
                            'reference_id' => $loan->id,
                            'description' => 'Loan deduction: ' . ($loan->loanType->name ?? 'Loan'),
                            'amount' => $deductionAmount,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                    } else {
                        // Loan fully paid, mark as complete
                        $loan->status = 'completed';
                        $loan->save();
                    }    
                }

                $salary_type = $employee['salary_type'];

                $cut_off_period = $other_service->splitDateRange($payroll->cut_off_period);

                $dtr = $dtr_service->getDailyTimeRecord($employee_no, $cut_off_period, true);

                $dtr_summary  = $dtr['summary'];

                $overtimeData = $payroll_service->computeOvertimePay($basic_salary, $dtr_summary['worked_days'], $dtr_summary['overtime_minues']);
                
                $overtime = $overtimeData['gross_ot_pay'];

                $holiday_pay = round($payroll_service->computeHolidayPayment($basic_salary, $dtr_summary, $salary_type));
                $allowances = 0;
                
                $aut = round(floatval($payroll_service->computeAutDeduction($dtr_summary, $basic_salary, $salary_type)));

                $sss = $hasDeductions ? $contribution_service->computeSSS($basic_salary)['employee_share'] ?? 0 : 0;
                $pagibig = $hasDeductions ? $contribution_service->computePagibig($basic_salary)['employee_share'] ?? 0 : 0;
                $philhealth = $hasDeductions ? $contribution_service->computePhilHealth($basic_salary)['employee_share'] ?? 0 : 0;

                $w_tax = $hasDeductions ? $contribution_service->computeWithholdingTax($basic_salary) : 0;
               // $other_loans = 0;

                $gross_amount_earned = $basic_salary + $overtime + $holiday_pay + $allowances;
                $total_deductions = $sss + $pagibig + $philhealth + $w_tax + $other_loans + $aut;
                $net_amount = $gross_amount_earned - $total_deductions;
                
                $bank_account = $employee['account_no'] ?? null;
                $bank_name = $employee['bank'] ?? null;

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'name' => $name,
                    'position' => $position,
                    'basic_salary' => $basic_salary,
                    'overtime_pay' => $overtime,
                    'holiday_pay' => $holiday_pay,
                    'allowances' => $allowances,
                    'aut' => $aut,
                    'gross_amount_earned' => round($gross_amount_earned, 2),
                    'sss' => round($sss, 2),
                    'pagibig' => round($pagibig, 2),
                    'philhealth' => round($philhealth, 2),
                    'w_tax' => round($w_tax, 2),
                    'other_loans' => round($other_loans, 2),
                    'loan_deductions' => $loanDeductionsToInsert,
                    'total_deductions' => round($total_deductions, 2),
                    'net_amount' => round($net_amount, 2),
                    'bank_account' => $bank_account,
                    'bank_name' => $bank_name,
                ];
            }

            return $data;

        }

    }
    


}