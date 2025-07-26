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

        $payroll->no_employees = $payroll->items->count();

        $netAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->net_amount));
        $salaryAmount = $payroll->items->sum(fn($item) => (float) str_replace(',', '', $item->salary));

        $payroll->overall_net_amount = round($netAmount, 2);
        $payroll->overall_salary = round($salaryAmount, 2);
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

        foreach ($chunks as $chunk) {
            $jobs[] = new PayrollJob(collect($chunk), $payroll, 'salary');
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

    public function computePayroll($payroll, $employees, $type) {

        $hasDeductions = $payroll->hasDeductions ?? false;

        if ($this->product == 'government') {

            $other_service = new OtherServices;
            $dtr_service = new DailyTimeRecordService;
            $leaveCard_service = new LeaveCardService;
            $payroll_service = app(PayrollService::class);

            $data = [];

            foreach ($employees as $employee) {
                $employee_no = $employee['employee_no'];
                $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $position = $employee['position_name'];
                $basic_salary = round(floatval($employee['salary']), 2);

                $bsd_no = !$this->bsd_emp_identical ? $employee['bsd_no'] : $employee['employee_no'];

                $monthYear = Carbon::parse($payroll->payroll_date)->format('m-Y');
                $cut_off_period = $payroll->cut_off_period;

                $earnings = $other_service->earnings($employee_no);
                $deductions = $hasDeductions ? $other_service->deductions($employee_no) : [];

                $current_date = Carbon::parse($payroll->payroll_date)->format('m/Y');
                $social_security = $hasDeductions
                    ? DB::table('social_security as gb')
                        ->join('social_security_items as gi', 'gb.id', '=', 'gi.social_security_id')
                        ->where('gb.billing_month', $current_date)
                        ->where('gi.crn_no', $employee['gsis_no'])
                        ->select('gi.consoloan', 'gi.emrgy_loan', 'gi.plreg', 'gi.mpl', 'gi.cpl')
                        ->first() ?? (object) []
                    : (object) [];

                // Earnings
                $pera = round(floatval(collect($earnings)->firstWhere('code', 'PERA')['amount'] ?? 0), 2);
                $gross = round($basic_salary + $pera, 2);

                // Deductions (based on flag)
                $rlip = $hasDeductions ? round(floatval($basic_salary * 0.09), 2) : 0;
                $philhealth = $hasDeductions ? round(floatval($basic_salary * 0.05 / 2), 2) : 0;
                $hdmf = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'HDMF')['amount'] ?? 0), 2) : 0;
                $mp2 = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'MP2')['amount'] ?? 0), 2) : 0;
                $mplstlms = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'MPLSTLMS')['amount'] ?? 0), 2) : 0;
                $cir = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0), 2) : 0;
                $w_tax = $hasDeductions ? round(floatval($payroll_service->computeWithholdingTax($basic_salary) ?? 0), 2) : 0;
                $uca = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'Unliquidated_Cash_Advances')['amount'] ?? 0), 2) : 0;
                $consoloan = $hasDeductions ? round(floatval($social_security->consoloan ?? 0), 2) : 0;
                $emergency_loan = $hasDeductions ? round(floatval($social_security->emrgy_loan ?? 0), 2) : 0;
                $plreg = $hasDeductions ? round(floatval($social_security->plreg ?? 0), 2) : 0;
                $mpl = $hasDeductions ? round(floatval($social_security->mpl ?? 0), 2) : 0;
                $cpl = $hasDeductions ? round(floatval($social_security->cpl ?? 0), 2) : 0;
                $aut = 0;

                // Optional deductions
                $dbp = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'DBP Savings')['amount'] ?? 0), 2) : 0;
                $kawani = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('deduction.code', 'Unlad Kawani')['amount'] ?? 0), 2) : 0;

                $total_deduction = round(
                    $rlip + $hdmf + $philhealth + $consoloan + $emergency_loan +
                    $plreg + $mpl + $cpl + $mp2 + $mplstlms + $cir + $w_tax + $aut
                );

                $net = round($gross - $total_deduction, 2);
                $half = round($net / 2, 2);

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'name' => $name,
                    'position' => $position,
                    'basic_salary' => $basic_salary,
                    'pera' => $pera,
                    'gross_amount_earned' => $gross,
                    'rlip' => $rlip,
                    'hdmf' => $hdmf,
                    'philhealth' => $philhealth,
                    'consoloan' => $consoloan,
                    'emergency_loan' => $emergency_loan,
                    'plreg' => $plreg,
                    'mpl' => $mpl,
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
                    'lbp_payroll_account' => $net,
                    'salary' => $half,
                ];
            }

            return $data;

        } else {

            $other_service = new OtherServices;
            $dtr_service = new DailyTimeRecordService;
            $leaveCard_service = new LeaveCardService;
            $contribution_service = new ContributionsService;

            $data = [];

            foreach ($employees as $employee) {
                $employee_no = $employee['employee_no'];
                $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $position = $employee['position_name'];
                $basic_salary = round(floatval($employee['salary']), 2);
                $employee_biometrics = !$this->bsd_emp_identical ? $employee['bsd_no'] : $employee['employee_no'];

                $monthYear = Carbon::parse($payroll->payroll_date)->format('m-Y');
                $cut_off_period = $payroll->cut_off_period;

                dd($monthYear, $cut_off_period);

                $this->summary_service->getSummary($employee_no);

                $overtime = 0;
                $holiday_pay = 0;
                $allowances = 0;
                $aut = 0;

                $sss = $contribution_service->computeSSS($basic_salary)['employee_share'] ?? 0;
                $pagibig = $contribution_service->computePagibig($basic_salary)['employee_share'] ?? 0;
                $philhealth = $contribution_service->computePhilHealth($basic_salary)['employee_share'] ?? 0;

                $w_tax = $contribution_service->computeWithholdingTax($basic_salary);
                $other_loans = 0;

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