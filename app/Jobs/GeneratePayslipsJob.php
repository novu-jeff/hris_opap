<?php

namespace App\Jobs;

use App\Jobs\GenerateSinglePayslipJob;
use App\Models\SalaryItemsPayroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePayslipsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $payrollId;

    public function __construct(
        int $payrollId
    ) {
        $this->payrollId = $payrollId;
    }

    public function handle(): void
    {

        SalaryItemsPayroll::where(
            'payroll_id',
            $this->payrollId
        )
        ->pluck('employee_no')
        ->each(function ($employeeNo) {

            GenerateSinglePayslipJob::dispatch(

                $employeeNo,

                $this->payrollId

            );

        });

    }
}