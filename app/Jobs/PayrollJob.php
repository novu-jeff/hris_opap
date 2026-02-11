<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use App\Http\Controllers\Admin\Services\PayrollService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batchable;
use App\Models\PayrollItems;
use Throwable;
use App\Models\Loan;

class PayrollJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employees;
    protected $payroll;
    protected $type;
    protected $payrollId;

   public function __construct(array $employees, int $payrollId, string $type)
{
    $this->employees = $employees;
    $this->payrollId = $payrollId;
    $this->type = $type;
    $this->onQueue('payroll');
}

    public function handle()
{
    $payroll = \App\Models\SalaryPayroll::find($this->payrollId);

   if (!$payroll) {
       \Log::error('PayrollJob: Payroll not found', ['payroll_id' => $this->payrollId]);
       return;
   }

   $service = app(PayrollService::class);
$process = $service->getProcess($this->type);

$instance = app($process['service']);

// Check if computePayroll exists
if (!method_exists($instance, 'computePayroll')) {
    \Log::error('computePayroll method does not exist on instance', [
        'instance_class' => get_class($instance),
        'process' => $process
    ]);
    return; // or throw exception
}


    $data = $instance->computePayroll($payroll, $this->employees, $this->type);
    foreach ($data as $item) {
        $payrollItem = $process['models']['child']::updateOrCreate([
                'payroll_id'  => $item['payroll_id'],
                'employee_no' => $item['employee_no'],
            ], $item);

        if (!empty($item['loan_deductions'])) {
            foreach ($item['loan_deductions'] as &$loanDeduction) {
                $loanDeduction['payroll_item_id'] = $payrollItem->id;
            }
            DB::table('payroll_salary_deductions')->insert($item['loan_deductions']);
        }

        // Update loan table
        foreach ($item['loan_deductions'] ?? [] as $ld) {
            $loan = Loan::find($ld['reference_id']);
            if ($loan) {
                $loan->balance -= $ld['amount'];
                $loan->last_posted_at = now();

                // If fully paid, mark as 'paid'
                if ($loan->balance <= 0) {
                    $loan->balance = 0;
                    $loan->status = 'completed';
                }

                $loan->save();
            }
        }
    }
}

    public function failed(Throwable $exception) {
        \Log::error('Payroll job failed: ' . $exception->getMessage());
    }
}
