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

    public function __construct($employees, $payroll, $type)
    {
        $this->employees = $employees;
        $this->payroll = $payroll;
        $this->type = $type;
    }

    public function handle()
    {
        \Log::info('Processing payroll items', [
        'payroll_id' => $this->payroll->id,
        'employees_count' => count($this->employees)
        ]);

        $service = app(PayrollService::class);

        $process = $service->getProcess($this->type);
        $serviceInstance = app($process['service']);

        $data = $serviceInstance->computePayroll($this->payroll, $this->employees, $this->type);

        foreach ($data as $item) {
            $process['models']['child']::updateOrCreate(
                [
                    'payroll_id'  => $item['payroll_id'],
                    'employee_no' => $item['employee_no'],
                ],
                $item
            );
        }
    }

    public function failed(Throwable $exception) {
        \Log::info('Error Processing Info: ' . $exception->getMessage());
    }
}
