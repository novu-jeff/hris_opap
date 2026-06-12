<?php

namespace App\Services;

use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Models\EmployeeInformation;

class PayslipPdfService
{
    public function generateAndSave(
        string $employeeNo,
        int $payrollId
    ): ?string {

        $payroll = SalaryItemsPayroll::with(
            'information.section',
            'payroll',
            'deductions.loan.loanType'
        )
        ->where('employee_no', $employeeNo)
        ->where('payroll_id', $payrollId)
        ->first();

        if (! $payroll) {

            logger()->error('Payroll not found', [
                'employee_no' => $employeeNo,
                'payroll_id' => $payrollId,
            ]);
        
            return null;
        }

        $payslipView = $this->buildPayslipViewData(
            $payroll
        );

        $supervisingOfficer =
            $this->getEmployeeByPosition(
                'Supervising Administrative Officer'
            );

        $directory =
            storage_path(
                "app/payslips/payroll_{$payrollId}"
            );

              

            $provider = [
                'client_logo' => config('meta.novulutions.client_logo'),
            ];

        if (! File::exists($directory)) {

            File::makeDirectory(
                $directory,
                0755,
                true
            );

        }

        // Sanitize employee name for use in filename
        $employeeName = strtoupper($payroll->name);

        $employeeName = preg_replace(
            '/[^A-Za-z0-9\s]/',
            '',
            $employeeName
        );

        $employeeName = preg_replace(
            '/\s+/',
            '_',
            trim($employeeName)
        );

        // Example:
        // EMP-001_JOHN_DOE.pdf
        $fileName =
            $employeeNo .
            '_' .
            $employeeName .
            '.pdf';

        $fullPath =
            $directory .
            '/' .
            $fileName;
        
            logger()->info('Rendering PDF', [
                'employee' => $fileName,
            ]);

        $pdf = Pdf::loadView(
            'admin.payslip-pdf',
            [
                'payslip' => $payroll,
                'payslipView' => $payslipView,
                'supervisingOfficer' => $supervisingOfficer,
                'provider' => $provider,
                'use_employee' => 0,
            ]
        );

        file_put_contents(
            $fullPath,
            $pdf->output()
        );

        logger()->info('PDF saved', [
            'path' => $fullPath,
            'exists' => file_exists($fullPath),
        ]);

        $relative =
            "payslips/payroll_{$payrollId}/{$fileName}";

            $payroll->update([
                'payslip_path' => $relative,
            ]);
            
            // ------------------------------------
            // Update generation progress
            // ------------------------------------
            
            SalaryPayroll::where('id', $payrollId)
                ->increment('payslip_generated');
            
            $salaryPayroll = SalaryPayroll::find($payrollId);
            
            if (
                $salaryPayroll &&
                $salaryPayroll->payslip_generated >=
                $salaryPayroll->payslip_total
            ) {
            
                $salaryPayroll->update([
                    'payslip_status' => 'completed',
                ]);
            
            }
            
            return $relative;
    }

    private function buildPayslipViewData($payslip)
    {
        [$start] =
            explode(
                ' to ',
                $payslip->payroll->cut_off_period
            );

        $startDate =
            Carbon::parse($start);

        return [

            'fullMonthCutoff' =>

                $startDate
                    ->copy()
                    ->startOfMonth()
                    ->format('F j')

                .

                ' – '

                .

                $startDate
                    ->copy()
                    ->endOfMonth()
                    ->format('F j, Y'),

            'monthLabel' =>

                $startDate
                    ->format('F Y'),

        ];
    }

    private function getEmployeeByPosition(
        $positionName
    ) {

        $supervisingOfficer =
            EmployeeInformation::join(
                'employee_personal as ep',
                'employee_information.employee_no',
                '=',
                'ep.employee_no'
            )

            ->join(
                'positions as p',
                'employee_information.position_id',
                '=',
                'p.id'
            )

            ->where(
                'p.name',
                $positionName
            )

            ->selectRaw(
                "
                p.name as pname,

                ep.firstname,

                ep.middlename,

                ep.lastname,

                ep.suffix,

                CONCAT(

                    ep.firstname,

                    ' ',

                    IFNULL(
                        ep.middlename,
                        ''
                    ),

                    ' ',

                    ep.lastname,

                    ' ',

                    IFNULL(
                        ep.suffix,
                        ''
                    )

                )

                as full_name
            "
            )

            ->first();

        if ($supervisingOfficer) {

            return [

                'full_name' =>

                    $supervisingOfficer
                        ->full_name,

                'position_name' =>

                    $supervisingOfficer
                        ->pname,

            ];

        }

        return [

            'full_name' => 'N/A',

            'position_name' => 'N/A',

        ];

    }
}