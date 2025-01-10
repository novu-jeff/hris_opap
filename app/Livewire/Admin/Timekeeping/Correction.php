<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
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
            ],
            'previous' => [
                'day' => $previousDay,
                'month' => $previousMonth,
                'year' => $previousYear,
            ],
            'next' => [
                'day' => $nextDay,
                'month' => $nextMonth,
                'year' => $nextYear,
            ],
            'data' => []
        ];
                
    }

    public function findLogs(int $id) {
        // Check if the same log is being clicked again
        if ($this->viewLogBsdNo === $id) {
            // Toggle visibility (hide)
            $this->viewLogBsdNo = null;
            $this->view_log = null;
        } else {
            // Set new log to be viewed
            $timestamp = Carbon::create($this->year, $this->month, $this->day)->format('Y-m-d');
            $data = EmployeeClockInOut::with('information.personal')
                ->orWhere('bsd_no', $id)
                ->whereDate('created_at', $timestamp)
                ->first();
    
            $this->viewLogBsdNo = $id;
            $this->view_log = $data;
        }
    }

    public function render() {

        $timestamp = $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($this->day, 2, '0', STR_PAD_LEFT);
    
        $model = EmployeeClockInOut::with('information.personal')
            ->whereDate('created_at', $timestamp)
            ->where(function ($query) {
                $query->whereNull('clock_in_am')
                    ->orWhereNull('clock_out_pm')
                    ->orWhereNull('clock_in_pm')
                    ->orWhereNull('clock_out_pm');
            });
        
        if ($this->search) {
            $this->resetPage();
            $model->where(function ($query) {
                $query->whereHas('information', function($subQuery) {
                        $subQuery->where('employee_no', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('information.personal', function ($subQuery) {
                        $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                    })
                    ->orWhere('bsd_no', 'like', '%' . $this->search . '%');
            });
        }

        $timelogs = $model->paginate($this->entries);

        return view('livewire.admin.timekeeping.correction', [
            'timelogs' => $timelogs
        ]);
    }
}
