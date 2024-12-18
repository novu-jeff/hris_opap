<?php

namespace App\Http\Controllers\Admin\TimeKeeping;

use App\Http\Controllers\Controller;
use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimekeepingController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(string $month = null, int $day = null, int $year = null)
    {

        if (is_null($month) || is_null($day) || is_null($year)) {
            // Get the latest record from the database
            $latestRecord = EmployeeClockInOut::orderByDesc('created_at')->first();
        
            if ($latestRecord) {
                // Get the year, month, and day of the latest record
                $year = $latestRecord->created_at->year;
                $month = sprintf('%02d', $latestRecord->created_at->month);
                $day = sprintf('%02d', $latestRecord->created_at->day);
            } else {
                // Use the current date if no latest record exists
                $currentDate = now();
                $year = $currentDate->year;
                $month = sprintf('%02d', $currentDate->month);
                $day = sprintf('%02d', $currentDate->day);
            }
        
            // Redirect to the resolved date
            return redirect()->route('timekeeping.index', ['month' => $month, 'day' => $day, 'year' => $year]);
        }
        

        $setup = request()->query('setup');

        return view('admin.timekeeping.index', compact('month', 'day', 'year', 'setup'));
    }

    public function upload()
    {
        return view('admin.timekeeping.upload');
    }

    public function correction(string $month = null, int $day = null, int $year = null)
    {

        if (is_null($month) || is_null($day) || is_null($year)) {
            // Get the latest record from the database
            $latestRecord = EmployeeClockInOut::orderByDesc('created_at')->first();
        
            if ($latestRecord) {
                // Get the year, month, and day of the latest record
                $year = $latestRecord->created_at->year;
                $month = sprintf('%02d', $latestRecord->created_at->month);
                $day = sprintf('%02d', $latestRecord->created_at->day);
            } else {
                // Use the current date if no latest record exists
                $currentDate = now();
                $year = $currentDate->year;
                $month = sprintf('%02d', $currentDate->month);
                $day = sprintf('%02d', $currentDate->day);
            }
        
            // Redirect to the resolved date
            return redirect()->route('timekeeping.correction', ['month' => $month, 'day' => $day, 'year' => $year]);
        }
        

        $setup = request()->query('setup');

        return view('admin.timekeeping.correction', compact('month', 'day', 'year', 'setup'));
    }

}
