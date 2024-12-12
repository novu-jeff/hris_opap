<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public $records;

    public function mount()
    {
       
        $records = EmployeeClockInOut::with('information.personal')
        ->get()
        ->groupBy(function ($record) {
            return Carbon::parse($record->created_at)->format('F, Y');
        })
        ->map(function ($group, $monthYear) {
            // Extract month and year separately
            $splitDate = explode(', ', $monthYear);
            return [
                'month' => $splitDate[0],
                'year' => $splitDate[1],
                'records' => $group, // Add grouped records if needed
            ];
        })
        ->values();

        $this->records = $records;
    }

    public function render()
    {
        return view('livewire.admin.reports.daily-time-record.index');
    }
}
