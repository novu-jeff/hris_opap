<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{

    public $month;
    public $day;
    public $year;
    public $setup;

    public $records;

    public bool $lazy = true;

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
        
        $this->getData();
        
    }

    public function getData() {
        // Create the timestamp for the current date
        $timestamp = $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($this->day, 2, '0', STR_PAD_LEFT);
    
        // Fetch the clock-in/clock-out records
        $records = EmployeeClockInOut::with('information.personal')->where('created_at', 'like', '%' . $timestamp . '%')->get();        
    
        // Set the data in the records array
        $this->records['data'] = $records;
    }

    public function render() {
        return view('livewire.admin.timekeeping.index');
    }
}
