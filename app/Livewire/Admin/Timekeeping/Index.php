<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $month;
    public $day;
    public $year;
    public $setup;

    public $records;

    public bool $lazy = true;

    public $entries = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['loading'];

    public function mount() {
        $this->loadRecords();
    }

    public function loading() {
        $this->dispatch('reinitializeDataTable');
        $this->lazy = false;
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


    public function render() {

        $timestamp = Carbon::create($this->year, $this->month, $this->day)->format('Y-m-d');

        $model = EmployeeClockInOut::with('information.personal')
            ->whereDate('created_at', $timestamp);
        
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

        return view('livewire.admin.timekeeping.index', [
            'timelogs' => $timelogs
        ]);
    }
}
