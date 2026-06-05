<?php

namespace App\Services;

use App\Models\EmployeeTimelogs;
use App\Models\EmployeeOvertime;
use App\Services\DailyTimeRecordService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClockInOutService
{
    public function process(string $entry, array $toProcess, string $employee_no): array
    {
        $this->checkLog((int) $entry, $toProcess, $employee_no);
        return $this->insertLog((int) $entry, $toProcess, $employee_no);
    }

    public function checkLog(int $entry, array $toProcess, string $employee_no): array
    {
        $timeMark = Carbon::parse($toProcess['timestamp']);
        $shift = app(DailyTimeRecordService::class)->getShiftSchedule($employee_no);
        $isFlexibleInOut = ($shift->shift_duration ?? '') === 'flexible-in-out';
        $hasBreakTime = !$isFlexibleInOut && ($shift->is_breaktime_required ?? false);

        return match (true) {
            $hasBreakTime => match ($entry) {
                0 => $this->validateClockIn($timeMark, $shift),
                1, 2 => $this->validateLunch($entry, $timeMark, $shift),
                3 => $this->validateClockOut($timeMark, $shift, $toProcess['timestamp'], $employee_no),
                4 => $this->validateAlreadyDone(),
                default => ['status' => true]
            },
            default => match ($entry) {
                0 => $this->validateClockIn($timeMark, $shift),
                1 => $this->validateClockOut($timeMark, $shift, $toProcess['timestamp'], $employee_no),
                2 => $this->validateAlreadyDone(),
                default => ['status' => true]
            }
        };
    }

    private function validateClockIn(Carbon $timeMark, $shift): array
    {
        if (in_array($shift->shift_duration ?? '', ['flexible', 'flexible-in-out'])) {
            $earliest = Carbon::parse($shift->earliest_in);
            $latest = Carbon::parse($shift->latest_in);

            return match (true) {
                $timeMark->lt($earliest) => $this->info("The earliest clock in is " . $earliest->format('g:i A')),
                $timeMark->gt($latest) => $this->confirm("You're clocking in and will be marked as late."),
                default => ['status' => true]
            };
        }

        $startShift = Carbon::parse($shift->start_shift);
        return $timeMark->gt($startShift)
            ? $this->confirm("You're clocking in and will be marked as late.")
            : ['status' => true];
    }

    private function validateLunch(int $entry, Carbon $timeMark, $shift): array
    {
        $breakOut = Carbon::parse($shift->break_out);
        $breakIn = Carbon::parse($shift->break_in);

        return match (true) {
            $entry === 1 && $timeMark->lt($breakOut) => $this->confirm("You're lunching out too early and will be marked as undertime."),
            $entry === 2 && $timeMark->gt($breakIn) => $this->confirm("You're lunching in too late and will be marked as undertime."),
            default => ['status' => true]
        };
    }

    private function validateClockOut(Carbon $timeMark, $shift, string $timestamp, string $employee_no): array
    {
        $duration = $shift->shift_duration ?? '';
        $expectedOut = in_array($duration, ['flexible', 'flexible-in-out'])
            ? optional($this->getFirstLog($timestamp, $employee_no))['timestamp'] ?? null
            : Carbon::parse($shift->end_shift);

        if (is_string($expectedOut)) {
            $expectedOut = Carbon::parse($expectedOut)->addHours($duration === 'flexible-in-out' ? 8 : 9);
        }

        return $timeMark->lt($expectedOut)
            ? $this->confirm("You're clocking out earlier than the expected {$expectedOut->format('g:i A')} and will be marked as undertime.")
            : ['status' => true];
    }

    private function validateAlreadyDone(): array
    {
        return $this->info("Today's job is already done");
    }

    private function getFirstLog(string $timestamp, string $employee_no): ?array
    {
        $date = Carbon::parse($timestamp)->toDateString();
        $employeeId = $this->getEmployeeID($employee_no);

        return EmployeeTimelogs::where('employee_id', $employeeId)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp')
            ->first()?->toArray();
    }

    public function insertLog(int $entry, array $toProcess, string $employee_no): array
    {
        $timestamp = $toProcess['timestamp'];
        $captured_location = isset($toProcess['captured_location']['coordinates'])
            ? implode(',', $toProcess['captured_location']['coordinates'])
            : null;

        $captured_image = $this->insertImage($employee_no, $toProcess['captured_image']);
        $accomplishment = $toProcess['accomplishment'] ?? null;
        $employeeId = $this->getEmployeeID($employee_no);

        $formattedTimestamp = Carbon::now()->format('Y-m-d') . ' ' . Carbon::parse($timestamp)->format('H:i');
        $statusMap = [0 => 0, 1 => 1, 2 => 0, 3 => 1];

        $data = [
            'employee_id' => $employeeId,
            'timestamp' => $formattedTimestamp,
            'isWeb' => true,
            'captured_location' => $captured_location,
            'captured_image' => $captured_image,
            'accomplishment' => $accomplishment,

            // Daily accomplishment
            'accomplishment_type' => $toProcess['accomplishment_type'] ?? null,
            'accomplishment_details' => $toProcess['accomplishment_details'] ?? null,
        ];

        if (config('app.external_timelogs')) {
            $data += [
                'sn' => 'RUU5242500021',
                'table' => 'ATTLOG',
                'stamp' => '9999',
                'status1' => $statusMap[$entry]
            ];
        } else {
            $data['status'] = $statusMap[$entry];
        }

     

        EmployeeTimelogs::create($data);

Log::debug('Entry', [
    'employeeId' => $entry
]);
        if (in_array($entry, [1, 3])) {
            Log::debug('Clock In and out service called', [
                'employeeId' => $employeeId,
                'formattedTimestamp' => $formattedTimestamp,
            ]);
            $this->computeDailyOvertime($employeeId, $formattedTimestamp);
        }

        return [
            'status' => true,
            'alert' => 'success',
            'title' => 'Recorded!',
            'message' => ''
        ];
    }

    private function insertImage(string $employee_no, string $imageData): ?string
    {
        if (!str_contains($imageData, 'base64,')) {
            \Log::error('Invalid image data format.', compact('employee_no'));
            return null;
        }

        [$header, $base64Data] = explode('base64,', $imageData);
        $decodedImage = base64_decode(str_replace(' ', '+', $base64Data));

        if ($decodedImage === false) {
            \Log::error('Failed to decode base64 image.', compact('employee_no'));
            return null;
        }

        $filename = strtolower($employee_no . '_' . time() . '.png');
        Storage::disk('public')->put("timelogs/{$filename}", $decodedImage);

        return $filename;
    }

    private function getEmployeeID(string $employee_no): ?string
    {
        $service = app(DailyTimeRecordService::class);
        return config('app.bsd_emp_identical') ? $employee_no : $service->getBsdNo($employee_no);
    }

    private function confirm(string $message): array
    {
        return [
            'status' => false,
            'alert' => 'confirm',
            'title' => 'Are you sure to continue?',
            'message' => $message
        ];
    }

    private function info(string $message): array
    {
        return [
            'status' => false,
            'alert' => 'info',
            'title' => 'Please be informed',
            'message' => $message
        ];
    }

    public function computeDailyOvertime(string $employeeId, string $timestamp): void
    {
        $date = Carbon::parse($timestamp)->toDateString();

        /*$logs = EmployeeTimelogs::where('employee_id', $employeeId)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp')
            ->get();*/
            $logs = EmployeeTimelogs::getLogsForPeriodFromBothSources(
                $employeeId,
                $date . ' 00:00:00',
                $date . ' 23:59:59'
            );    

        $totalMinutes = 0;
        $timeIn = null;

        foreach ($logs as $log) {
            if ($log->status == 0) {
                $timeIn = Carbon::parse($log->timestamp);
            }

            if ($log->status == 1 && $timeIn) {
                $timeOut = Carbon::parse($log->timestamp);

                if ($timeOut->greaterThan($timeIn)) {
                    $totalMinutes += $timeIn->diffInMinutes($timeOut);
                }

                $timeIn = null;
            }
        }

       /* $requiredMinutes = 8 * 60;

        $overtimeMinutes = max(0, $totalMinutes - $requiredMinutes);
        $overtimeHours = round($overtimeMinutes / 60, 2);*/

        $requiredMinutes = 8 * 60;
        $minimumClaimableOt = 2 * 60;

        $renderedOtMinutes = max(
            0,
            $totalMinutes - $requiredMinutes
        );

        $overtimeMinutes = $renderedOtMinutes >= $minimumClaimableOt
            ? $renderedOtMinutes
            : 0;

        $overtimeHours = round($overtimeMinutes / 60, 2);

        // save to attendance or overtime table
        EmployeeOvertime::updateOrCreate(
            [
                'employee_no' => $employeeId,
                'work_date' => $date,
            ],
            [
                'total_minutes' => $totalMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'overtime_hours' => $overtimeHours,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
