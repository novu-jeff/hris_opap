<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployeeTimelogs;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EmployeeAccomplishmentReportController extends Controller
{
    /**
     * Download Employee Daily Accomplishment Report
     */
    public function download($employeeNo, $date)
    {
        // Employee Information
        $employee = EmployeeInformation::with('personal')
        ->where(function ($query) use ($employeeNo) {
            $query->where('employee_no', $employeeNo)
                  ->orWhere('bsd_no', $employeeNo);
        })
        ->firstOrFail();
        // Timelogs for the selected date
        $logs = EmployeeTimelogs::where('employee_id', $employeeNo)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp')
            ->get();

        if ($logs->isEmpty()) {
            abort(404, 'No accomplishment record found.');
        }

        $clockIn  = $logs->get(0);
        $lunchOut = $logs->get(1);
        $lunchIn  = $logs->get(2);
        $clockOut = $logs->last();

        $pdf = Pdf::loadView(
            'employee.pdf.employee-accomplishment-report',
            [
                'employee'  => $employee,
                'date'      => Carbon::parse($date),
                'clockIn'   => $clockIn,
                'lunchOut'  => $lunchOut,
                'lunchIn'   => $lunchIn,
                'clockOut'  => $clockOut,
            ]
        );

        return $pdf->download(
            'Accomplishment_Report_' .
            $employee->employee_no .
            '_' .
            Carbon::parse($date)->format('Ymd') .
            '.pdf'
        );
    }


    public function downloadMonthly()
    {
        $employeeNo = auth()->user()->employee_no;

        $employee = EmployeeInformation::with('personal')
            ->where('employee_no', $employeeNo)
            ->firstOrFail();

        $logs = EmployeeTimelogs::where('employee_id', $employeeNo)
            ->whereMonth('timestamp', now()->month)
            ->whereYear('timestamp', now()->year)
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->timestamp)->format('Y-m-d');
            });

        $pdf = Pdf::loadView(
            'employee.pdf.employee-accomplishment-report-monthly',
            [
                'employee' => $employee,
                'month'    => now(),
                'logs'     => $logs,
            ]
        );

        return $pdf->download(
            'Monthly_Accomplishment_Report_' .
            $employee->employee_no .
            '_' .
            now()->format('Ym') .
            '.pdf'
        );
    }


}
