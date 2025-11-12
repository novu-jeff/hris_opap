<?php

namespace App\Console\Commands;

use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateLeaveCard extends Command
{
    protected $signature = 'generate:leave-card';
    protected $description = 'Generate leave card for employees when month is December';

    public function handle()
    {
        $months = [
            "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
            "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
        ];

        EmployeeInformation::orderBy('id')->chunk(100, function ($employees) use ($months) {
            foreach ($employees as $employee) {
                $records = EmployeeLeaveCard::where('employee_no', $employee->employee_no)->get();

                if ($records->isEmpty()) {
                    continue;
                }

                $grouped = $records->groupBy('year')->sortKeys();
                $lastYear = $grouped->keys()->last();
                $newYear = $lastYear + 1;

                $lastRecord = $grouped[$lastYear]->sortBy(function ($item) {
                    return Carbon::parse($item->period)->month;
                })->last();

                if (!$lastRecord) {
                    continue;
                }

                $vlBalance = (float) $lastRecord->vl_bal ?? 0;
                $slBalance = (float) $lastRecord->sl_bal ?? 0;

                DB::transaction(function () use ($employee, $months, $newYear, &$vlBalance, &$slBalance) {
                    foreach ($months as $month) {
                        $vlBalance += 1.25;
                        $slBalance += 1.25;

                        $record = EmployeeLeaveCard::firstOrNew([
                            'employee_no' => $employee->employee_no,
                            'year' => $newYear,
                            'period' => $month,
                        ]);

                        $record->fill([
                            'particulars'       => '',
                            'vl_earned'         => 1.25,
                            'vl_aut_w_pay'      => 0,
                            'vl_aut_wo_pay'     => 0,
                            'vl_bal'            => round($vlBalance, 3),
                            'sl_earned'         => 1.25,
                            'sl_aut_w_pay'      => 0,
                            'sl_aut_wo_pay'     => 0,
                            'sl_bal'            => round($slBalance, 3),
                            'remarks'           => '',
                        ])->save();
                    }
                });
            }
        });

        $this->info('Leave card generated: ' . now()->format('Y-m-d H:i:s'));
    }
}
