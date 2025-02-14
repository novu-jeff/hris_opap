<?php

namespace App\Console\Commands;

use App\Models\EmployeeAUT;
use App\Models\EmployeeInformation;
use App\Models\EmployeeTimelogs;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ComputeAUT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compute-aut';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run command after uploading bulk logs';


    private function getLogs() {

        $records = EmployeeTimelogs::where('isComputed', true)
            ->get();
        
        $groupedData = $records->groupBy(function ($record) {
            try {
                $date = Carbon::createFromFormat('d/m/Y H:i', $record->logdatetime)->format('j/n/Y');
            } catch (\Exception $e) {
                return null; // Skip invalid dates
            }
            return $date . '|' . ($record->bsd_no ?? 'undefined');
        })->filter()->map(function ($logs, $key) {
            [$date, $bsd_no] = explode('|', $key);
        
            // Extract logs and sort by time
            $logEntries = $logs->sortBy(function ($log) {
                try {
                    return Carbon::createFromFormat('d/m/Y H:i', $log->logdatetime);
                } catch (\Exception $e) {
                    return null;
                }
            })->values();
        
            if ($logEntries->count() === 2 && !empty($logEntries->last()->accomplishment)) {
                return [
                    'date' => $date,
                    'bsd_no' => $bsd_no,
                    'origin' => $logs->first()->origin,
                    'logs' => [
                        [
                            'time' => Carbon::createFromFormat('d/m/Y H:i', $logEntries[0]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[0]->captured_image,
                            'captured_location' => $logEntries[0]->captured_location
                        ],
                        [], // Empty array for consistency
                        [], // Empty array for consistency
                        [
                            'time' => Carbon::createFromFormat('d/m/Y H:i', $logEntries[1]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[1]->captured_image,
                            'captured_location' => $logEntries[1]->captured_location,
                            'accomplishment' => $logEntries[1]->accomplishment
                        ]
                    ]
                ];
            }
        
            if ($logEntries->count() === 3 && !empty($logEntries->last()->accomplishment)) {
                return [
                    'date' => $date,
                    'bsd_no' => $bsd_no,
                    'origin' => $logs->first()->origin,
                    'logs' => [
                        [
                            'time' => Carbon::createFromFormat('d/m/Y H:i', $logEntries[0]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[0]->captured_image,
                            'captured_location' => $logEntries[0]->captured_location
                        ],
                        [
                            'time' => Carbon::createFromFormat('d/m/Y H:i', $logEntries[1]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[1]->captured_image,
                            'captured_location' => $logEntries[1]->captured_location,
                            'accomplishment' => $logEntries[1]->accomplishment
                        ],
                        [], // Empty array for consistency
                        [
                            'time' => Carbon::createFromFormat('d/m/Y H:i', $logEntries[2]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[2]->captured_image,
                            'captured_location' => $logEntries[2]->captured_location,
                            'accomplishment' => $logEntries[2]->accomplishment
                        ]
                    ]
                ];
            }
        
            return [
                'date' => $date,
                'bsd_no' => $bsd_no,
                'origin' => $logs->first()->origin,
                'logs' => collect($logs)->map(function ($log) {
                    try {
                        $time = Carbon::createFromFormat('d/m/Y H:i', $log->logdatetime)->format('H:i:s');
                    } catch (\Exception $e) {
                        return null;
                    }
                    return [
                        'time' => $time,
                        'captured_image' => $log->captured_image,
                        'captured_location' => $log->captured_location,
                        'accomplishment' => $log->accomplishment
                    ];
                })->filter()->values()->all()
            ];
        })->values();
    
    
        return $groupedData;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $aut = [];

        $records = $this->getLogs();

        foreach ($records as $record) {

            $earliestIn = Carbon::parse('07:00');
            $latestIn = Carbon::parse('09:00');
            
            $logs = $record['logs'];

            usort($logs, function ($a, $b) {
                return Carbon::parse($a['time'])->greaterThan(Carbon::parse($b['time']));
            });
        
            // Earliest Log
            $log = Carbon::parse($logs[0]['time']);
        
            if ($log > $latestIn) {
                $lateMins = $log->diffInMinutes($latestIn);
        
                $aut[$record['date']][$record['bsd_no']] = [
                    'late' => $lateMins
                ];
            }
        
            $firstLog = Carbon::parse($logs[0]['time']);

            if($firstLog->lessThan($earliestIn)) {
                $expectedOut = $earliestIn->copy()->addHours(9);
            } else if($firstLog->between($earliestIn, $latestIn)) {
                $expectedOut = $firstLog->copy()->addHours(9);
            } else {
                $expectedOut = Carbon::parse('18:00');
            }

            // $outLog = isset($logs[3]['time']) ? Carbon::parse($logs[3]['time']) : $expectedOut;
            $outLog = !empty($logs) && count($logs) > 1 ? Carbon::parse(end($logs)['time']) : $expectedOut;

            // end($logs['])
            if ($outLog->lessThan($expectedOut) || $outLog->equalTo($expectedOut)) {
                $undertimeMinutes = $expectedOut->diffInMinutes($outLog);
    
                $aut[$record['date']][$record['bsd_no']]['outLog'] = $outLog->format('h:i');
                $aut[$record['date']][$record['bsd_no']]['expectedOut'] = $expectedOut->format('h:i');
                $aut[$record['date']][$record['bsd_no']]['undertime'] = $undertimeMinutes;
            }
        }

        $batchInsert = [];
        $bsdNumbers = [];
        
        foreach ($aut as $date => $employees) {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $date)->format('d/m/Y');
            foreach ($employees as $bsd_no => $values) {
                $batchInsert[] = [
                    'date' => $formattedDate,
                    'bsd_no' => $bsd_no,
                    'lates' => $values['late'] ?? 0,
                    'undertime' => $values['undertime'] ?? 0,
                    'absences' => $values['absences'] ?? 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
        
                $bsdNumbers[$formattedDate][] = $bsd_no;
            }
        }
        
        // Bulk insert EmployeeAUT records
        if (!empty($batchInsert)) {
            EmployeeAUT::insert($batchInsert);
        }
        
        // Bulk update EmployeeTimelogs using LIKE and WHERE IN
        foreach ($bsdNumbers as $date => $bsdNos) {
            EmployeeTimelogs::where('logdatetime', 'like', "%$date%")
                ->whereIn('bsd_no', $bsdNos)
                ->update(['isComputed' => true]);
        }     

    }
}