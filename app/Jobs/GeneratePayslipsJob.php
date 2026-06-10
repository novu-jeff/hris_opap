<?php

namespace App\Jobs;

use App\Services\PayslipGeneratorService;
use App\Services\PayslipPdfService;
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

    /**
     * Create a new job instance.
     */
    public function __construct(int $payrollId)
    {
        $this->payrollId = $payrollId;
    }

    /**
     * Execute the job.
     */
    public function handle(PayslipPdfService $service): void
    {
        SalaryItemsPayroll::where(
            'payroll_id',
            $this->payrollId
        )
        ->pluck('employee_no')
        ->each(function ($employeeNo) use ($service) {

            try {

                logger()->info('Generating payslip', [
                    'employee_no' => $employeeNo,
                    'payroll_id' => $this->payrollId,
                ]);

                $service->generateAndSave(
                    $employeeNo,
                    $this->payrollId
                );

            } catch (\Throwable $e) {

                logger()->error('GeneratePayslipsJob failed', [
                    'employee_no' => $employeeNo,
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]);

            }

        });
    }
}