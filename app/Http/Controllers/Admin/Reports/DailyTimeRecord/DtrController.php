<?php

namespace App\Http\Controllers\Admin\Reports\DailyTimeRecord;

use ZipArchive;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\ShiftSchedule;
use App\Services\DailyTimeRecordService;
use Barryvdh\DomPDF\Facade\Pdf;

class DtrController extends Controller
{
    
    public function downloadAll($month, $year, $employees)
    {
        set_time_limit(0);

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
//dd($month, $year);
        $service = app(DailyTimeRecordService::class);

        $employeeShift = ShiftSchedule::first();

        $officialTime = [
            'current_time' => now()->format('h:i A'),
            'shift_duration' => $employeeShift->shift_duration ?? 'N/A',
        ];

        $provider = [
            'client_logo' => config('meta.novulutions.client_logo'),
        ];

        $tempDir = storage_path('app/dtr-temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $zipName = "DTR-{$month}-{$year}.zip";

        $zipPath = $tempDir . '/' . $zipName;

        $zip = new ZipArchive();

        if (
            $zip->open(
                $zipPath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            ) !== true
        ) {
            abort(500, 'Unable to create ZIP file.');
        }
        $employeeNos = explode(',', $employees);
         EmployeeInformation::with([
            'personal',
            'section',
            'positions'
        ])
        ->whereIn('employee_no', $employeeNos)
        ->chunkById(25, function ($employees) use (
            $zip,
            $service,
            $month,
            $year,
            $officialTime,
            $provider
        ) {

            foreach ($employees as $employee) {

                try {

                    if (!$employee->personal) {
                        continue;
                    }

                    \Log::info('Generating DTR PDF', [
                        'employee_no' => $employee->employee_no
                    ]);

                    $monthDate = Carbon::parse(
                        $month . ' ' . $year
                    )->format('m-Y');

                    $dtrLogs = $service->getDailyTimeRecord(
                        $employee->employee_no,
                        $monthDate,
                        true
                    );

                    $logs = [
                        'employee_account' => [
                            'employee_no' => $employee->employee_no,
                            'firstname' => $employee->personal->firstname ?? '',
                            'middlename' => $employee->personal->middlename ?? '',
                            'lastname' => $employee->personal->lastname ?? '',
                            'position' => $employee->positions->name ?? '',
                            'section' => $employee->section->name ?? '',
                        ],
                        'dtr' => $dtrLogs
                    ];

                    $pdf = Pdf::loadView(
                        'admin.reports.dtr.pdf',
                        [
                            'logs' => $logs,
                            'officialTime' => $officialTime,
                            'dtrDate' => Carbon::parse(
                                $month . ' ' . $year
                            ),
                            'provider' => $provider,
                        ]
                    )
                    ->setPaper('A4', 'portrait');

                    $fileName =
                        $employee->employee_no .
                        '-' .
                        preg_replace(
                            '/[^A-Za-z0-9]/',
                            '_',
                            $employee->personal->lastname ?? 'UNKNOWN'
                        ) .
                        '.pdf';

                    $zip->addFromString(
                        $fileName,
                        $pdf->output()
                    );

                    unset($pdf);

                    gc_collect_cycles();

                } catch (\Throwable $e) {

                    \Log::error('DTR PDF Failed', [
                        'employee_no' => $employee->employee_no,
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);

                    continue;
                }
            }
        });

        $zip->close();

        return response()
            ->download($zipPath)
            ->deleteFileAfterSend(true);
    }
}