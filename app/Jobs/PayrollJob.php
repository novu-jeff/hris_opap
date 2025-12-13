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
}

    public function handle()
{
   \Log::info('PayrollJob handle() STARTED', [
        'payroll_id' => $this->payrollId
    ]);
    $payroll = \App\Models\SalaryPayroll::find($this->payrollId);


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


  \Log::info('Processing payroll here', [
        'payroll_id' => $this->payrollId,
        'employees_count' => count($this->employees)
    ]);

    $data = $instance->computePayroll($payroll, $this->employees, $this->type);

    foreach ($data as $item) {
        $process['models']['child']::updateOrCreate([
            'payroll_id'  => $item['payroll_id'],
            'employee_no' => $item['employee_no'],
        ], $item);
    }
}

    public function failed(Throwable $exception) {
        \Log::info('Error Processing Info: ' . $exception->getMessage());
    }
}
