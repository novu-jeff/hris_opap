<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ComputeAUT extends Command
{
    protected $signature = 'compute-aut';
    protected $description = 'Run command after uploading bulk logs';

    protected TimeLogService $timelogService;
    protected LeaveCardService $leaveCardService;

    public function __construct(TimeLogService $timelogService, LeaveCardService $leaveCardService)
    {
        parent::__construct();
        $this->timelogService = $timelogService;
        $this->leaveCardService = $leaveCardService;
    }

    public function handle()
    {
        $now = Carbon::now();
        $monthFormatted = strtoupper($now->format('F'));
        $yearFormatted = $now->format('Y');
        $monthYear = $now->format('m-Y');

        $months = [
            'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
            'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
        ];

        $currentIndex = array_search($monthFormatted, $months);
        if ($currentIndex === false) {
            Log::warning("Month {$monthFormatted} not found in months array.");
            return;
        }

        $batchSize = 100;  
        $batchNumber = 0;

        EmployeeInformation::where('employment_type_id', 1)
            ->chunk($batchSize, function ($employees) use (
                $monthFormatted, $yearFormatted, $monthYear, $months, $currentIndex, &$batchNumber
            ) {
                $batchNumber++;

                foreach ($employees as $employee) {
                    try {
                        $bio_id = $employee->bsd_no;
                        $employee_no = $employee->employee_no;

                        $logs = $this->timelogService->getDTR($bio_id, $monthYear);
                        $aut = $logs['summary']['less_aut'] ?? 0;
                        $converted = $aut * 0.002;

                        $leaveCard = $this->leaveCardService->leaveCard($employee_no);
                        $items = collect($leaveCard[$yearFormatted]['items'] ?? []);

                        $currentItem = $items->firstWhere('period', $monthFormatted);
                        if (!$currentItem) continue;

                        $prevItem = $currentIndex > 0 ? $items->firstWhere('period', $months[$currentIndex - 1]) : null;

                        $prevBal = $prevItem['vl_bal'] ?? 0;
                        $vlEarned = $currentItem['vl_earned'] ?? 0;
                        $computedBal = $prevBal + $vlEarned - $converted;

                        $currentDb = EmployeeLeaveCard::find($currentItem['id']);
                        if (!$currentDb) continue;

                        if ($computedBal < 0) {
                            $currentDb->vl_aut_w_pay = $prevBal + $vlEarned;
                            $currentDb->vl_aut_wo_pay = $converted - ($prevBal + $vlEarned);
                            $currentDb->vl_bal = null;
                        } else {
                            $currentDb->vl_aut_w_pay = $converted;
                            $currentDb->vl_aut_wo_pay = null;
                            $currentDb->vl_bal = $computedBal;
                        }

                        $autFormatted = $this->formatTime($aut);
                        $autEntry = "AUT: {$autFormatted}";

                        $existingParticulars = $currentDb->particulars ?? '';

                        if (Str::contains($existingParticulars, 'AUT:')) {
                            $existingParticulars = preg_replace('/AUT:\s*[^,]*/', $autEntry, $existingParticulars);
                        } else {
                            $existingParticulars = trim($existingParticulars);
                            $existingParticulars = $existingParticulars === '' ? $autEntry : "{$existingParticulars}, {$autEntry}";
                        }

                        $currentDb->particulars = $existingParticulars;
                        $currentDb->save();

                        // Update future leave balances
                        $runningBalance = $currentDb->vl_bal ?? 0;
                        $remainingItems = $items->filter(function ($item) use ($months, $currentIndex) {
                            $index = array_search(strtoupper($item['period']), $months);
                            return $index !== false && $index > $currentIndex;
                        });

                        foreach ($remainingItems as $item) {
                            $earned = $item['vl_earned'] ?? 0;
                            $runningBalance += $earned;

                            $dbItem = EmployeeLeaveCard::find($item['id']);
                            if (!$dbItem) continue;

                            $dbItem->vl_bal = $runningBalance;
                            $dbItem->save();
                        }
                    } catch (\Throwable $e) {
                        Log::error("Error processing employee #{$employee->employee_no}: {$e->getMessage()}");
                    }
                }

                // Log progress with batch number
                Log::info("AUT computed {$batchNumber} batch(es) at " . now());
            });
    }


    private function formatTime($totalMinutes)
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $result = '';

        if ($hours > 0) {
            $result .= $hours . 'hr';
            if ($minutes > 0) {
                $result .= ' ';
            }
        }

        if ($minutes > 0) {
            $result .= $minutes . 'mins';
        }

        if (empty($result)) {
            $result = '0mins';
        }

        return $result;
    }
}
