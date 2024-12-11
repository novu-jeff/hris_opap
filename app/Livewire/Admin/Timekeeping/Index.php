<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{

    public $month;
    public $year;

    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        if (is_null($this->year) && is_null($this->month)) {
            // Get the latest record
            $latestRecord = EmployeeClockInOut::orderByDesc('created_at')->first();
        
            // Extract the year and month from the 'created_at' of the latest record
            $year = $latestRecord->created_at->year; // Extracts the year (e.g., 2024)
            $month = sprintf('%02d', $latestRecord->created_at->month); // Formats the month as two digits
        
            $timestamp = $year . '-' . $month;
            
            // Get all records that match the same year and month
            $recordsForMonth = EmployeeClockInOut::where('created_at', 'like', '%' . $timestamp . '%')
                ->get();
        } else {
            // Use the provided year and month
            $year = $this->year;
            $month = sprintf('%02d', $this->month);
        
            $timestamp = $year . '-' . $month;
        
            // Get records for the given month and year
            $recordsForMonth = EmployeeClockInOut::where('created_at', 'like', '%' . $timestamp . '%')
                ->get();
        }
        
        // Calculate the current, previous, and next months
        $currentMonth = Carbon::createFromFormat('Y-m', $timestamp);
        
        // Get the previous month
        $previousMonth = $currentMonth->copy()->subMonth();
        $previousMonthFormatted = $previousMonth->format('Y-m');  // Format previous month as 'yyyy-mm'
        
        // Get the next month
        $nextMonth = $currentMonth->copy()->addMonth();
        $nextMonthFormatted = $nextMonth->format('Y-m');  // Format next month as 'yyyy-mm'
        
        // Display the records, including previous and next months
        $this->records = [
            'previous' => [
                'month' => $previousMonth->month,
                'year' => $previousMonth->year
            ],
            'next' => [
                'month' => $nextMonth->month,
                'year' => $nextMonth->year
            ],
            'data' => $recordsForMonth
        ];
        

    }

    public function render()
    {
        return view('livewire.admin.timekeeping.index');
    }
}
