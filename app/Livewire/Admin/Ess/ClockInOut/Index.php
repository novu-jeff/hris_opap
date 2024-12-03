<?php

namespace App\Livewire\Admin\Ess\ClockInOut;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{

    public $records;

    public function mount() {
        
        $records = EmployeeClockInOut::with('information.personal')
            ->get()
            ->groupBy(function ($record) {
                return Carbon::parse($record->created_at)->format('F d, Y'); // Group by date
            })
            ->map(function ($group, $date) {
                // Calculate totals
                $totalClockIn = $group->count();
                $totalClockOut = $group->whereNotNull('clock_out')->count();
                $inProgress = $group->whereNull('clock_out')->count();

                // Structure the result
                return [
                    'info' => [
                        'date' => $date,
                        'total' => [
                            'clockin' => $totalClockIn,
                            'clockout' => $totalClockOut,
                            'in_progress' => $inProgress,
                        ],
                    ],
                    'data' => $group->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'employee_no' => $record->information->employee_no,
                            'clock_in' => $record->clock_in,
                            'clock_out' => $record->clock_out,
                            'captured_image_clockin' => $record->captured_image_clockin,
                            'captured_image_clockout' => $record->captured_image_clockout,
                            'captured_location_clockin' => $record->captured_location_clockin,
                            'captured_location_clockout' => $record->captured_location_clockout,
                            'created_at' => $record->created_at,
                            'updated_at' => $record->updated_at,
                            'information' => $record->information->toArray(),
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray();
            
        // dd($records);

        $this->records = $records;
    
    }

    public function render()
    {
        return view('livewire.admin.ess.clock-in-out.index');
    }
    
}
