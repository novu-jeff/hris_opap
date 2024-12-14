<?php

namespace App\Services;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\returnSelf;

class DailyTimeRecordService {

    public function getDailyTimeRecord($id, $date)
    {
        $date = Carbon::createFromFormat('F, Y', $date);

        Log::info('Showing DTR for Date: ' . $date);

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $employeeClockQuery = DB::table('employee_clock_in_out')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orWhereNull('created_at');

        $dtr = DB::table('employee_account')
            ->leftJoin('employee_information', 'employee_account.employee_no', '=', 'employee_information.employee_no')
            ->leftJoinSub($employeeClockQuery, 'clock_data', function ($join) {
                $join->on('employee_information.bsd_no', '=', 'clock_data.bsd_no');
            })
            ->leftJoin('employee_personal', 'employee_account.employee_no', '=', 'employee_personal.employee_no')
            ->leftJoin('positions', 'employee_information.position_id', '=', 'positions.id')
            ->leftJoin('sections', 'employee_information.section_id', '=', 'sections.id')
            ->leftJoin('departments', 'sections.department_id', '=', 'departments.id')
            ->select(
                'employee_account.employee_no',
                'employee_information.bsd_no',
                'employee_personal.firstname',
                'employee_personal.middlename',
                'employee_personal.lastname',
                'clock_data.clock_in_am',
                'clock_data.clock_out_am',
                'clock_data.clock_in_pm',
                'clock_data.clock_out_pm',
                'clock_data.total_mins_consumed',
                'clock_data.created_at',
                'clock_data.origin',
                'positions.code as position_code',
                'positions.name as position_name',
                'positions.salary',
                'departments.name as department_name',
                'departments.code as department_code'
            )
            ->where('employee_account.employee_no', $id)
            ->get();


        $allDays = collect();
        foreach ($startDate->toPeriod($endDate) as $day) {
            $allDays->push($day->toDateString()); 
        }

        $mappedClockData = [];
        foreach ($allDays as $day) {

            $clockData = $dtr->firstWhere(function($item) use ($day) {
                return Carbon::parse($item->created_at)->isSameDay(Carbon::parse($day));
            });

            $mappedClockData[] = [
                'date' => $day,
                'clock_in_am' => $clockData ? $clockData->clock_in_am : null,
                'clock_out_am' => $clockData ? $clockData->clock_out_am : null,
                'clock_in_pm' => $clockData ? $clockData->clock_in_pm : null,
                'clock_out_pm' => $clockData ? $clockData->clock_out_pm : null,
                'origin' => $clockData ? $clockData->origin : null,
                'total_mins_consumed' => $clockData ? $clockData->total_mins_consumed : null,
            ];
        }

        // Assign data to $this->dtr
        $dtr_new_format = [
            'employee_account' => [
                'employee_no' => $dtr->first()->employee_no,
                'firstname' => $dtr->first()->firstname,
                'lastname' => $dtr->first()->lastname,
                'middlename' => $dtr->first()->middlename ? strtoupper(substr($dtr->first()->middlename, 0, 1)) . '.' : '',
                'position' => $dtr->first()->position_name . ' (' . $dtr->first()->position_code . ')',
                'department' => $dtr->first()->department_name . ' (' . $dtr->first()->department_code . ')',
                'salary' => $dtr->first()->salary,
            ],
            'clock_in_out' => $mappedClockData
        ];

        return $dtr_new_format;
    }
}