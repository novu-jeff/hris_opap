<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SalaryItemsPayroll;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class PayslipGeneratorService
{
    public function generate(int $payrollId): void
    {
        $directory = storage_path(
            "app/payslips/payroll_{$payrollId}"
        );

        // Create directory if it does not exist
        if (! File::exists($directory)) {
            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        $provider = [
            'client_logo' => config('app.client_logo'),
        ];

        // Delete old PDFs if regenerating
        foreach (glob($directory . '/*.pdf') as $file) {
            @unlink($file);
        }

        $supervisingOfficer = $this->getSupervisingOfficer();

        SalaryItemsPayroll::with([
            'information.section',
            'payroll',
            'deductions.loan.loanType',
        ])
        ->where('payroll_id', $payrollId)
        ->chunkById(
            25,
            function ($items) use (
                $directory,
                $supervisingOfficer,
                $provider,
                $payrollId,
            ) {

                foreach ($items as $item) {

                    try {

                        $payslipView = $this->buildPayslipViewData(
                            $item
                        );

                        $pdf = Pdf::loadView(
                            'admin.payslip-pdf',
                            [
                                'payslip' => $item,
                                'payslipView' => $payslipView,
                                'supervisingOfficer' => $supervisingOfficer,
                                'provider' => $provider,
                                'use_employee' => 0,
                            ]
                        );

                        $fileName =
                            $item->employee_no . '.pdf';

                        $fullPath =
                            $directory . '/' . $fileName;

                        file_put_contents(
                            $fullPath,
                            $pdf->output()
                        );

                        // Save path in DB
                        SalaryItemsPayroll::where('id', $item->id)
                            ->update([
                                'payslip_path' => "payslips/payroll_{$payrollId}/{$fileName}",
                            ]);

                        unset($pdf);
                        unset($payslipView);

                    } catch (\Throwable $e) {

                        logger()->error(
                            'Payslip generation failed',
                            [
                                'employee_no' => $item->employee_no,
                                'message' => $e->getMessage(),
                            ]
                        );
                    }
                }

                gc_collect_cycles();
            },
            'id'
        );
    }

    /**
     * Get supervising officer.
     */
    protected function getSupervisingOfficer(): array
    {
        $supervisingOfficer = \DB::table('employee_information')
            ->join(
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
                'Supervising Administrative Officer'
            )
            ->selectRaw("
                p.name as pname,
                ep.firstname,
                ep.middlename,
                ep.lastname,
                ep.suffix,
                CONCAT(
                    ep.firstname,
                    ' ',
                    IFNULL(ep.middlename,''),
                    ' ',
                    ep.lastname,
                    ' ',
                    IFNULL(ep.suffix,'')
                ) as full_name
            ")
            ->first();

        if ($supervisingOfficer) {
            return [
                'full_name' => $supervisingOfficer->full_name,
                'position_name' => $supervisingOfficer->pname,
            ];
        }

        return [
            'full_name' => 'N/A',
            'position_name' => 'N/A',
        ];
    }

    /**
     * Copy your existing buildPayslipViewData()
     * from your Livewire component here.
     */
    private function buildPayslipViewData($payslip)
    {
        [$start] = explode(' to ', $payslip->payroll->cut_off_period);
        $startDate = Carbon::parse($start);

        return [
            'fullMonthCutoff' =>
                $startDate->copy()->startOfMonth()->format('F j')
                . ' – ' .
                $startDate->copy()->endOfMonth()->format('F j, Y'),

            'monthLabel' => $startDate->format('F Y'),
        ];
    }
}
