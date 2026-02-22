<?php

namespace App\Models;

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
     * @param string|int $employeeId Employee ID (bsd_no when bsd_emp_identical is false, else employee_no)
     * @param string $startDate Start datetime (Y-m-d H:i:s)
     * @param string $endDate End datetime (Y-m-d H:i:s)
     * @return Collection Collection of objects with timestamp, shift_id, schedule_id, isWeb, etc. (sorted by timestamp)
     */
    public static function getLogsForPeriodFromBothSources($employeeId, $startDate, $endDate): Collection
    {
        $normalized = collect();

        // 1) System timelogs (mysql.timelogs)
        $employeNo = self::getBsdNo($employeeId);
        $shiftId = EmployeeInformation::where('employee_no', $employeeId)
            ->value('shift_id'); 
        $fromTimelogs = DB::connection('mysql')
            ->table('timelogs')
            ->where('employee_id', $employeeId)
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->orderBy('timestamp')
            ->get();

            Log::info('employeeId', [$employeeId]);
            Log::info('startDate', [$startDate]);
            Log::info('endDate', [$endDate]);
            Log::info('fromTimelogs', [$fromTimelogs]);

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

        // 2) Biometrics attendances (mysql2.attendances)
        $employeNo = self::getBsdNo($employeeId);
        $shiftId = EmployeeInformation::where('employee_no', $employeeId)
            ->value('shift_id');    

        Log::info('employeNo', [$employeNo]);
        Log::info('startDate', [$startDate]);
        Log::info('endDate', [$endDate]);
        $fromAttendances = DB::connection('mysql2')
            ->table('attendances')
            ->where('employee_id', $employeNo)
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->orderBy('timestamp')
            ->get();

            logger('fromAttendances', [$fromAttendances]);
            Log::info('fromAttendances', [$fromAttendances]);

        foreach ($fromAttendances as $row) {
            $normalized->push((object) [
                'timestamp' => $row->timestamp,
                'employee_id' => $row->employee_id,
                'shift_id' => $shiftId,
                'schedule_id' => 1,
                'isWeb' => $row->isWeb ?? null,
                'captured_image' => $row->captured_image ?? null,
                'captured_location' => $row->captured_location ?? null,
                'accomplishment' => $row->accomplishment ?? null,
            ]);
        }

        return $normalized->sortBy('timestamp')->values();
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
