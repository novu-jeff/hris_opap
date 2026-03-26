<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeTimelogs extends Model
{
    use HasFactory;

    /**
     * Get timelogs from both sources (system timelogs + biometrics attendances) for a period.
     * Use this when generating payroll so attendance from both DBs is included.
     *
     * @param string|int $employeeNo Employee number (HRIS employee_no, e.g. "OP-21-226")
     * @param string $startDate Start datetime (Y-m-d H:i:s)
     * @param string $endDate End datetime (Y-m-d H:i:s)
     * @return Collection Collection of objects with timestamp, shift_id, schedule_id, isWeb, etc. (sorted by timestamp)
     */
    public static function getLogsForPeriodFromBothSources($employeeNo, $startDate, $endDate): Collection
    {
       
      // dd($employeeNo, $startDate, $endDate);
        $normalized = collect();
        $bsd_no = self::getBsdNo($employeeNo);
        $shiftId = EmployeeInformation::where('employee_no', $employeeNo)->value('shift_id');
        if ($shiftId === null && $bsd_no !== null) {
            $shiftId = EmployeeInformation::where('bsd_no', $bsd_no)->value('shift_id');
        }

        // 1) System timelogs (mysql.timelogs) – uses employee_no
        $fromTimelogs = DB::connection('mysql')
            ->table('timelogs')
            ->where('employee_id', $employeeNo)
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->orderBy('timestamp')
            ->get();

        foreach ($fromTimelogs as $row) {
            $normalized->push((object) [
                'timestamp' => $row->timestamp,
                'employee_id' => $row->employee_id,
                'shift_id' => $shiftId ?? 1,
                'schedule_id' => $row->schedule_id ?? 1,
                'isWeb' => $row->isWeb ?? null,
                'captured_image' => $row->captured_image ?? null,
                'captured_location' => $row->captured_location ?? null,
                'accomplishment' => $row->accomplishment ?? null,
            ]);
        }

        // 2) Biometrics attendances (mysql2.attendances / oppap_logs)
        // employee_id is BIGINT, so only query numeric ids to avoid string->0 coercion
        $attendancesIds = array_values(array_unique(array_filter(
            [$bsd_no, $employeeNo],
            fn($v) => $v !== null && $v !== '' && preg_match('/^\d+$/', (string) $v)
        )));
        $fromAttendances = collect();
        if (!empty($attendancesIds)) {
            $fromAttendances = DB::connection('mysql2')
                ->table('attendances')
                ->whereIn('employee_id', $attendancesIds)
                ->whereBetween('timestamp', [$startDate, $endDate])
                ->orderBy('timestamp')
                ->get();
        }

        foreach ($fromAttendances as $row) {
            $normalized->push((object) [
                'timestamp' => $row->timestamp,
                'employee_id' => $row->employee_id,
                'shift_id' => $shiftId ?? 1,
                'schedule_id' => 1,
                'isWeb' => $row->isWeb ?? null,
                'captured_image' => $row->captured_image ?? null,
                'captured_location' => $row->captured_location ?? null,
                'accomplishment' => $row->accomplishment ?? null,
            ]);
        }

        return $normalized->sortBy('timestamp')->values();
    }

    /**
     * True if mysql2 (e.g. oppap_logs / opapp_logs) `attendances` has any row for this employee on the given date
     * (biometric / device punches). Only numeric employee_id values are queried; string IDs cannot match BIGINT.
     */
    public static function hasExternalAttendanceOnDate(string $employeeNo, ?Carbon $date = null): bool
    {
        $date = $date ?? Carbon::now();
        $bsdNo = self::getBsdNo($employeeNo);
        $ids = array_values(array_unique(array_filter(
            [$bsdNo, $employeeNo],
            fn ($v) => $v !== null && $v !== '' && preg_match('/^\d+$/', (string) $v)
        )));
        if ($ids === []) {
            return false;
        }

        try {
            return DB::connection('mysql2')
                ->table('attendances')
                ->whereIn('employee_id', $ids)
                ->whereDate('timestamp', $date->format('Y-m-d'))
                ->exists();
        } catch (\Throwable $e) {
            Log::warning('EmployeeTimelogs::hasExternalAttendanceOnDate: '.$e->getMessage());

            return false;
        }
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $external = config('app.external_timelogs');
       // dd( $external );

        $this->setConnection($external ? 'mysql2' : 'mysql');

        $this->setTable($external ? 'attendances' : 'timelogs');

        $this->fillable = $external
            ? [
                'sn',
                'table',
                'stamp',
                'employee_id',
                'timestamp',
                'status1',
                'isWeb',
                'captured_image',
                'captured_location',
                'accomplishment',
            ]
            : [
                'employee_id',
                'timestamp',
                'status',
                'isWeb',
                'captured_image',
                'captured_location',
                'accomplishment',
            ];
    }

    public function employee()
    {

        $bsd_emp_identical = config('app.bsd_emp_identical');

        if(!$bsd_emp_identical) {
            return $this->belongsTo(EmployeeInformation::class, 'employee_id', 'bsd_no');
        } 

        return $this->belongsTo(EmployeeInformation::class, 'employee_id', 'employee_no');

    }

    public static function getBsdNo($employee_no)
    {
        return EmployeeInformation::where('employee_no', $employee_no)
            ->value('bsd_no');
    }
}
