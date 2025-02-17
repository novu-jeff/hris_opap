<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class TimeKeepingController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read timelogs')->only('index');
        $this->middleware('permission:write timelogs')->only(['create', 'edit']);
        $this->middleware('permission:read correction-timelogs')->only(['correction']);
        $this->middleware('permission:read correction-timelogs')->only(['correction_apply']);
    }

    public function index(string $month = null, int $day = null, int $year = null)
    {

        if (is_null($month) || is_null($day) || is_null($year)) {
            // Get the latest record from the database
            $latestRecord = EmployeeTimelogs::orderByDesc('logdatetime')->first();
            if ($latestRecord) {
                // Get the year, month, and day of the latest record
                $formattedDate = Carbon::createFromFormat('d/m/Y H:i', $latestRecord->logdatetime);
                $year = $formattedDate->format('Y');
                $month = $formattedDate->format('m');
                $day = $formattedDate->format('d');
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
            $latestRecord = EmployeeTimelogs::orderByDesc('created_at')->first();
        
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

    public function job(string $id) {
        return Bus::findBatch($id);
    }

    public function correction_apply(string $bsd_no, string $date) {
        return view('admin.timekeeping.correction-apply', compact('bsd_no', 'date'));
    }

}
