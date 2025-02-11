<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Correction extends Component
{

    use WithPagination;

    public $month;
    public $day;
    public $year;
    public $setup;

    public $records;
    public $view_log;
    public $viewLogBsdNo;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        // Default to the current day, month, and year
        $currentDate = Carbon::createFromDate($this->year, $this->month, $this->day);


        // Calculate the previous date
        $previousDate = $currentDate->copy()->subDay();
        $previousDay = $previousDate->day;
        $previousMonth = $previousDate->month;
        $previousYear = $previousDate->year;

        // Calculate the next date
        $nextDate = $currentDate->copy()->addDay();
        $nextDay = $nextDate->day;
        $nextMonth = $nextDate->month;
        $nextYear = $nextDate->year;

        // Populate the records array
        $this->records = [
            'current' => [
                'day' => $this->day,
                'month' => Carbon::createFromFormat('m', $this->month)->format('F'),
                'year' => Carbon::createFromFormat('Y', $this->year)->format('Y'),
                'day_of_week' => $currentDate->format('l'),
            ],
            'previous' => [
                'day' => $previousDay,
                'month' => $previousMonth,
                'year' => $previousYear,
                'day_of_week' => $previousDate->format('l'),
            ],
            'next' => [
                'day' => $nextDay,
                'month' => $nextMonth,
                'year' => $nextYear,
                'day_of_week' => $nextDate->format('l'),
            ],
            'data' => []
        ];
                
    }

    public function findLogs(int $id) {

        if ($this->viewLogBsdNo === $id) {
            $this->viewLogBsdNo = null;
            $this->view_log = null;
        } else {
            $data = $this->getLogs($id)[0] ?? [];
            $this->viewLogBsdNo = $id;
            $this->view_log = $data;

        }
    }

        private function getLogs(int $bsd_no = null)
        {
            $timestamp = Carbon::create($this->year, $this->month, $this->day)->format('j/n/Y');
        
            $query = EmployeeTimelogs::with('employee.personal')
                ->where('logdatetime', 'LIKE', "{$timestamp}%");
        
            if (!is_null($bsd_no)) {
                $query->where('bsd_no', $bsd_no);
            }
        
            $records = $query->get()->unique('logdatetime'); // Remove exact duplicates
        
            $groupedData = $records->groupBy(function ($record) {
                return Carbon::parse($record->logdatetime)->format('j/n/Y') . '|' . ($record->bsd_no ?? 'undefined');
            })->map(function ($logs, $key) {
                [$date, $bsd_no] = explode('|', $key);
        
                $logEntries = collect($logs)->mapToGroups(function ($log) {
                    return [
                        Carbon::parse($log->logdatetime)->format('H') => [
                            'time' => Carbon::parse($log->logdatetime)->format('H:i:s'),
                            'captured_image' => $log->captured_image
                        ]
                    ];
                })->map(function ($entries, $hour) {
                    // Determine min/max for AM/PM logs
                    if ($hour == 12 || $hour == 13) {
                        return $entries->toArray(); // Store all values for 12 PM and 1 PM
                    } elseif ($hour < 12) {
                        return [$entries->sortBy('time')->first()]; // Keep earliest time for AM
                    } else {
                        return [$entries->sortByDesc('time')->first()]; // Keep latest time for PM
                    }
                })->collapse()->values()->all();
        
                // Ensure at least 3 log entries exist before adding to final dataset
                if (count($logEntries) < 3) {
                    return null;
                }
        
                return [
                    'date' => $date,
                    'bsd_no' => $bsd_no,
                    'employee' => $logs->first()->employee,
                    'origin' => $logs->first()->origin,
                    'logs' => $logEntries
                ];
            })->filter()->values(); // Remove null values (records with less than 3 logs)
        
            return $groupedData;
        }    

    public function render() {

        $data = $this->getLogs();

        // Manual pagination for collections
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $this->entries;
        $pagedData = $data->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $paginatedLogs = new LengthAwarePaginator(
            $pagedData, 
            count($data),
            $perPage, 
            $currentPage, 
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.admin.timekeeping.correction', [
            'timelogs' => $paginatedLogs
        ]);
        
    }
}
