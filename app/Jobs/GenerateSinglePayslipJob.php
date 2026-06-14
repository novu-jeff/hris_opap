<?php

namespace App\Jobs;

use App\Services\PayslipPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSinglePayslipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $employeeNo;

    public int $payrollId;

    public function __construct(
        string $employeeNo,
        int $payrollId
    ) {
        $this->employeeNo = $employeeNo;
        $this->payrollId = $payrollId;
    }

    public function handle(
        PayslipPdfService $service
    ): void {

        try {

            logger()->info('GenerateSinglePayslipJob', [
                'employee_no' => $this->employeeNo,
                'payroll_id' => $this->payrollId,
            ]);
    
            $service->generateAndSave(
                $this->employeeNo,
                $this->payrollId
            );
    
        } catch (\Throwable $e) {
    
            logger()->error('GenerateSinglePayslipJob failed', [
                'employee_no' => $this->employeeNo,
                'payroll_id' => $this->payrollId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
    
        }
    }
}