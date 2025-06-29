<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Http\Controllers\Admin\Services\LeaveCardService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batchable;
use Throwable;

class ProcessPayroll implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bsd_emp_identical;
    protected $employees;
    protected $payroll;

    public function __construct($employees, $payroll)
    {
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->employees = $employees;
        $this->payroll = $payroll;
    }

    public function handle()
    {
        $other_service = new OtherServices;
        $dtr_service = new TimeLogService;
        $leaveCard_service = new LeaveCardService;
        
        $data = [];

        foreach ($this->employees as $employee) {
            $employee_no = $employee->employee_no;
            $employee_name = trim($employee->firstname . ' ' . $employee->lastname);
            $employee_position = $employee->position_name;
            $employee_salary = round(floatval($employee->monthly_rate), 2);
            $employee_biometrics = !$this->bsd_emp_identical ? $employee->bsd_no : $employee->employee_no;

            $monthYear = Carbon::parse($this->payroll->payroll_date)->format('m-Y');
            $cut_off_period = $this->payroll->cut_off_period;

            $dtr = $dtr_service->getDTRByRange($employee_biometrics, $monthYear, $cut_off_period);
            $earnings = $other_service->earnings($employee_no);
            $deductions = $other_service->deductions($employee_no);

            $current_date = Carbon::parse($this->payroll->payroll_date)->format('m/Y');
            $social_security = DB::table('social_security as gb')
                ->join('social_security_items as gi', 'gb.id', '=', 'gi.social_security_id')
                ->where('gb.billing_month', $current_date)
                ->where('gi.crn_no', $employee->gsis_no)
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
            $w_tax = round(floatval($employee->w_tax ?? 0), 2);
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
                'payroll_id' => $this->payroll->id,
                'employee_no' => $employee_no,
                'employment_type' => $employee->employment_type_id,
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

        DB::table('payroll_items')->insert($data);
    }

    public function failed(Throwable $exception) {
        \Log::info('Error: ' . $exception->getMessage());
    }
}
