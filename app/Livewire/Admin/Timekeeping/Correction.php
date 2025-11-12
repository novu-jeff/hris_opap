<?php

namespace App\Livewire\Admin\Timekeeping;

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

        $currentDate = Carbon::createFromDate($this->year, $this->month, $this->day);

        $previousDate = $currentDate->copy()->subDay();
        $previousDay = $previousDate->day;
        $previousMonth = $previousDate->month;
        $previousYear = $previousDate->year;

        $nextDate = $currentDate->copy()->addDay();
        $nextDay = $nextDate->day;
        $nextMonth = $nextDate->month;
        $nextYear = $nextDate->year;

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
        $timestamp = Carbon::create($this->year, $this->month, $this->day)->format('d/m/Y');
        
        $query = EmployeeTimelogs::with('employee.personal')
            ->where('logdatetime', 'LIKE', "{$timestamp}%");
    
        if (!is_null($bsd_no)) {
            $query->where('bsd_no', $bsd_no);
        }
    
        $records = $query->get();
    
        return $records->groupBy(function ($record) {
            return Carbon::createFromFormat('d/m/Y H:i', $record->logdatetime)->format('d/m/Y') . '|' . ($record->bsd_no ?? 'undefined');
        })->map(function ($logs, $key) {
            [$date, $bsd_no] = explode('|', $key);
        
            $processedLogs = $this->processLogs($logs);
        
            return [
                'date' => $date,
                'bsd_no' => $bsd_no,
                'employee' => $logs->first()->employee,
                'origin' => $logs->first()->origin,
                'logs' => $processedLogs
            ];
        })->filter(function ($item) {
            return count($item['logs']) !== 4; 
        })->values();
        
    }

    private function processLogs($logs)
    {
        return $logs->mapToGroups(function ($log) {
            $logTime = Carbon::createFromFormat('d/m/Y H:i', $log->logdatetime);
            return [
                $logTime->format('H') => [
                    'time' => $logTime->format('H:i'),
                    'captured_location' => $log->captured_location,
                    'captured_image' => $log->captured_image,
                    'accomplishment' => $log->accomplishment
                ]
            ];
        })->map(function ($entries, $hour) {
            if ($hour == 12 || $hour == 13) {
                return $entries->values()->toArray();
            } elseif ($hour < 12) {
                return [$entries->sortBy('time')->first()];
            } else {
                return [$entries->sortByDesc('time')->first()]; 
            }
        })->collapse()->values()->all();
    }

    public function render() {

        $data = $this->getLogs();

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
