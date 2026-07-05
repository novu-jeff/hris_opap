<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EmployeeOffsetRequest;
use App\Models\EmployeeInformation;
use App\Models\EmployeeOffsetCredit;
use App\Models\EmployeeAccount;

class OffsetPdfService
{
    public function download($id)
    {
        $record = EmployeeOffsetRequest::with([
            'employee.personal'
        ])
        ->where('id', $id)
        ->where('status', 'approved')
        ->firstOrFail();

        $employee = EmployeeInformation::with([
            'personal',
            'positions',
        ])
        ->where('employee_no', $record->employee_no)
        ->first();

           

        $earnedHours = EmployeeOffsetCredit::where(
            'employee_no',
            $record->employee_no
        )->sum('earned_hours');

        $remainingHours = EmployeeOffsetCredit::where(
            'employee_no',
            $record->employee_no
        )->sum('remaining_hours');

       // $recommendedBy = EmployeeAccount::find($record->recommended_by);

       // $approvedBy = EmployeeAccount::find($record->approved_by);

        $provider = [
            'client_logo' => config('meta.novulutions.client_logo'),
        ];

        $pdf = Pdf::loadView(
            'employee.pdf.offset.application',
            compact(
                'record',
                'employee',
                'earnedHours',
                'remainingHours',
               // 'recommendedBy',
               // 'approvedBy',
                'provider'
            )
        );

        $pdf->setPaper('A4');

        return $pdf->download(
            'Authority_to_Render_Offsetting_'.$record->office_order_no.'.pdf'
        );
        // $pdf->save(storage_path('app/test.pdf'));

        // dd('saved');
    }
}