<?php

namespace App\Http\Controllers\Admin\Services\Payroll;

use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Jobs\PayrollJob;
use App\Models\EmployementTypes;
use App\Models\SalaryPayroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $payroll->overall_salary_amount = round($salaryAmount, 2);
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
                Rule::unique('payroll_salary')
                    ->where(function ($query) use($payload) {
                        return $query->where('cut_off_period', $payload['cut_off_period'])
                                    ->where('employment_type', $payload['employment_type']);
                    }),
            ],
            'cut_off_period' => [
                'required',
                'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/',
            ],
            'employment_type' => 'exists:employment_types,id',
        ];
    }


    public function createPayroll($payload) {
        if (SalaryPayroll::where([
            'payroll_date' => $payload['payroll_date'],
            'cut_off_period' => $payload['cut_off_period'],
            'employment_type' => $payload['employment_type'],
        ])->exists()) {
            throw new \Exception('Payroll for this period and employment type already exists.');
        }

        $payroll = SalaryPayroll::create([
            'payroll_date' => $payload['payroll_date'],
            'cut_off_period' => $payload['cut_off_period'],
            'employment_type' => $payload['employment_type'],
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
        $employees = $employees['eligible'];

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
        
        $other_service = new OtherServices;
        $dtr_service = new TimeLogService;
        $leaveCard_service = new LeaveCardService;

        if($this->product == 'government') {

            $data = [];

            foreach ($employees as $employee) {
                $employee_no = $employee['employee_no'];
                $employee_name = trim($employee['firstname'] . ' ' . $employee['lastname']);
                $employee_position = $employee['position_name'];
                $employee_salary = round(floatval($employee['monthly_rate']), 2);
                $employee_biometrics = !$this->bsd_emp_identical ? $employee['bsd_no'] : $employee['employee_no'];

                $monthYear = Carbon::parse($payroll->payroll_date)->format('m-Y');
                $cut_off_period = $payroll->cut_off_period;

                // $dtr = $dtr_service->getDTRByRange($employee_biometrics, $monthYear, $cut_off_period);
                $earnings = $other_service->earnings($employee_no);
                $deductions = $other_service->deductions($employee_no);

                $current_date = Carbon::parse($payroll->payroll_date)->format('m/Y');
                $social_security = DB::table('social_security as gb')
                    ->join('social_security_items as gi', 'gb.id', '=', 'gi.social_security_id')
                    ->where('gb.billing_month', $current_date)
                    ->where('gi.crn_no', $employee['gsis_no'])
                    ->select('gi.consoloan', 'gi.emrgy_loan', 'gi.plreg', 'gi.mpl', 'gi.cpl')
                    ->first() ?? (object) [];

                $aut = 0;
                $pera = round(floatval(collect($earnings)->firstWhere('code', 'PERA')['amount'] ?? 0), 2);
                $gross = round($employee_salary + $pera, 2);
                $rlip = round(floatval($employee_salary * 0.09), 2);
                $philhealth = round(floatval($employee_salary * 0.05 / 2), 2);

                $hdmf = round(floatval(collect($deductions)->firstWhere('deduction.code', 'HDMF')['amount'] ?? 0), 2);
                $mp2 = round(floatval(collect($deductions)->firstWhere('deduction.code', 'MP2')['amount'] ?? 0), 2);
                $mplstlms = round(floatval(collect($deductions)->firstWhere('deduction.code', 'MPLSTLMS')['amount'] ?? 0), 2);
                $cir = round(floatval(collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0), 2);
                $w_tax = round(floatval($employee['w_tax'] ?? 0), 2);
                $uca = round(floatval(collect($deductions)->firstWhere('deduction.code', 'Unliquidated_Cash_Advances')['amount'] ?? 0), 2);
                $dbp = round(floatval(collect($deductions)->firstWhere('deduction.code', 'DBP Savings')['amount'] ?? 0), 2);
                $kawani = round(floatval(collect($deductions)->firstWhere('deduction.code', 'Unlad Kawani')['amount'] ?? 0), 2);

                $consoloan = round(floatval($social_security->consoloan ?? 0), 2);
                $emergency_loan = round(floatval($social_security->emrgy_loan ?? 0), 2);
                $plreg = round(floatval($social_security->plreg ?? 0), 2);
                $mpl = round(floatval($social_security->mpl ?? 0), 2);
                $cpl = round(floatval($social_security->cpl ?? 0), 2);

                $total_deduction = round(
                    floatval($rlip) +
                    $hdmf +
                    floatval($philhealth) +
                    $consoloan +
                    $emergency_loan +
                    $plreg +
                    $mpl +
                    $cpl +
                    $mp2 +
                    $mplstlms +
                    $cir +
                    $w_tax +
                    floatval($aut)
                );

                $net = round($gross - $total_deduction, 2);
                $half = round($net / 2, 2);

                $data[] = [
                    'payroll_id' => $payroll->id,
                    'employee_no' => $employee_no,
                    'name' => $employee_name,
                    'position' => $employee_position,
                    'basic_salary' => $employee_salary,
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
                    'aut' => floatval($aut),
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

        }

    }

}