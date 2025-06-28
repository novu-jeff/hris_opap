<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Http\Controllers\Admin\Services\TimeLogService;
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
    public bool $isBsdEmpIdentical;

    public $entries = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public $logs = [];

    public function mount()
    {
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

    public function updatedDay() { $this->loadRecords(); }
    public function updatedMonth() { $this->loadRecords(); }
    public function updatedYear() { $this->loadRecords(); }

    public function loadRecords()
    {
        $this->isBsdEmpIdentical = config('app.bsd_emp_identical') ? true : false;

        $currentDate = Carbon::createFromDate($this->year, $this->month, $this->day);
        $previousDate = $currentDate->copy()->subDay();
        $nextDate = $currentDate->copy()->addDay();

        $this->logs = $this->getLogs();

        $this->records = [
            'current' => [
                'day' => $this->day,
                'month' => $currentDate->format('F'),
                'year' => $currentDate->format('Y'),
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

    public function findLogs(string $id)
    {
        if ($this->viewLogBsdNo === $id) {
            $this->viewLogBsdNo = null;
            $this->view_log = null;
        } else {
            $this->viewLogBsdNo = $id;
            $this->view_log = $this->logs[$id] ?? [];
        }
    }

    private function getLogs(?int $bsd_no = null)
    {
        $timestamp = Carbon::create($this->year, $this->month, $this->day)->format('Y-m-d');
        $logService = new TimeLogService;
        $logs = $logService->getLogs($timestamp, $bsd_no);

        return $logs[$timestamp] ?? [];
    }

    public function render()
    {
        $filtered = collect($this->logs);

        if (!empty($this->search)) {
            $filtered = $filtered->filter(function ($log, $key) {
                return str_contains(strtolower($key), strtolower($this->search));
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $this->entries;
        $pagedData = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedLogs = new LengthAwarePaginator(
            $pagedData,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.admin.timekeeping.index', [
            'timelogs' => $paginatedLogs,
        ]);
    }
}
