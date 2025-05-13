<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $month;
    public $day;
    public $year;
    public $setup;

    public $page;
    public $records;
    public $view_log;
    public $viewLogBsdNo;

    public $entries = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function mount() {
        $this->loadRecords();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function loadRecords() {
        $currentDate = Carbon::createFromDate($this->year, $this->month, $this->day);

        $previousDate = $currentDate->copy()->subDay();
        $nextDate = $currentDate->copy()->addDay();

        $this->records = [
            'current' => [
                'day' => $this->day,
                'month' => Carbon::createFromFormat('m', $this->month)->format('F'),
                'year' => Carbon::createFromFormat('Y', $this->year)->format('Y'),
                'day_of_week' => $currentDate->format('l'),
            ],
            'previous' => [
                'day' => $previousDate->day,
                'month' => $previousDate->month,
                'year' => $previousDate->year,
                'day_of_week' => $previousDate->format('l'),
            ],
            'next' => [
                'day' => $nextDate->day,
                'month' => $nextDate->month,
                'year' => $nextDate->year,
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
            $data = $this->getLogs()[$id] ?? [];
            $this->viewLogBsdNo = $id;
            $this->view_log = $data;
        }
    }

    private function getLogs(?int $bsd_no = null)
    {
        $timestamp = Carbon::create($this->year, $this->month, $this->day)->format('Y-m-d');

        $logService = new TimeLogService;

        $logs = $logService->getLogs($timestamp, $bsd_no);

        return $logs ? $logs[$timestamp] : [];

    }

    public function render()
    {
        $data = collect($this->getLogs()); 
    
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $this->entries;
        $pagedData = $data->slice(($currentPage - 1) * $perPage, $perPage)->values();
    
        $paginatedLogs = new LengthAwarePaginator(
            $pagedData,
            $data->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    
        return view('livewire.admin.timekeeping.index', [
            'timelogs' => $paginatedLogs
        ]);
    }
}
