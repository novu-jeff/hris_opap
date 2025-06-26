<?php

namespace App\Jobs;

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

class ProcessPayroll implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employees;
    protected $payroll;

    public function __construct($employees, $payroll)
    {
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
            $employee_name = $employee->firstname . ' ' . $employee->lastname;
            $employee_position = $employee->position_name;
            $employee_salary = round($employee->monthly_rate, 2);
            $employee_biometrics = $employee->bsd_no;

            $monthYear = Carbon::parse($this->payroll->payroll_date)->format('m-Y');
            $cut_off_period = $this->payroll->cut_off_period;

            $dtr = $dtr_service->getDTRByRange($employee_biometrics, $monthYear, $cut_off_period);
            $earnings = $other_service->earnings($employee_no);
            $deductions = $other_service->deductions($employee_no);

            $current_date = Carbon::parse($this->payroll->payroll_date)->format('m/Y');
            $gsis_billing = DB::table('gsis_billing as gb')
                ->join('gsis_billing_items as gi', 'gb.id', '=', 'gi.gsis_billing_id')
                ->where('gb.billing_month', $current_date)
                ->where('gi.crn_no', $employee->gsis_no)
                ->select('gi.consoloan', 'gi.emrgy_loan', 'gi.plreg', 'gi.mpl', 'gi.cpl')
                ->first();

            $aut = 0;
            $pera = round(collect($earnings)->firstWhere('code', 'PERA')['amount'] ?? 0, 2);
            $gross = $employee_salary + $pera;
            $rlip = round($employee_salary * 0.09, 2);
            $philhealth = round($employee_salary * 0.05 / 2, 2);

            $total_deduction = round(
                $rlip + round(collect($deductions)->firstWhere('deduction.code', 'HDMF')['amount'] ?? 0, 2) +
                $philhealth + ($gsis_billing->consoloan ?? 0) + ($gsis_billing->emrgy_loan ?? 0) +
                ($gsis_billing->plreg ?? 0) + ($gsis_billing->mpl ?? 0) + ($gsis_billing->cpl ?? 0) +
                round(collect($deductions)->firstWhere('deduction.code', 'MP2')['amount'] ?? 0, 2) +
                round(collect($deductions)->firstWhere('deduction.code', 'MPLSTLMS')['amount'] ?? 0, 2) +
                round(collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0, 2) +
                round($employee->w_tax ?? 0, 2) + $aut
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
                'hdmf' => round(collect($deductions)->firstWhere('deduction.code', 'HDMF')['amount'] ?? 0, 2),
                'philhealth' => $philhealth,
                'consoloan' => round($gsis_billing->consoloan ?? 0, 2),
                'emergency_loan' => round($gsis_billing->emrgy_loan ?? 0, 2),
                'plreg' => round($gsis_billing->plreg ?? 0, 2),
                'mpl' => round($gsis_billing->mpl ?? 0, 2),
                'cpl' => round($gsis_billing->cpl ?? 0, 2),
                'mp2' => round(collect($deductions)->firstWhere('deduction.code', 'MP2')['amount'] ?? 0, 2),
                'mplstlms' => round(collect($deductions)->firstWhere('deduction.code', 'MPLSTLMS')['amount'] ?? 0, 2),
                'cir375_cir449' => round(collect($deductions)->firstWhere('deduction.code', 'CIR375, CIR449')['amount'] ?? 0, 2),
                'w_tax' => round($employee->w_tax ?? 0, 2),
                'uca' => round(collect($deductions)->firstWhere('deduction.code', 'Unliquidated_Cash_Advances')['amount'] ?? 0, 2),
                'aut' => $aut,
                'total_deductions' => $total_deduction,
                'net_amount' => $net,
                'dbp' => round(collect($deductions)->firstWhere('deduction.code', 'DBP Savings')['amount'] ?? 0, 2),
                'kawani' => round(collect($deductions)->firstWhere('deduction.code', 'Unlad Kawani')['amount'] ?? 0, 2),
                'lbp_payroll_account' => $net,
                'salary' => $half,
            ];
        }

        DB::table('payroll_items')->insert($data);
    }

    public function failed(Throwable $exception) {
        // send notification;
    }
}
