<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDailyTimeRecordController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read employee-dtr')->only('index');
    }

    public function index(Request $request)
    {

        $employee_no = Auth::user()->employee_no;

        $month = $request->input('month'); 
        $year = $request->input('year');

        if(empty($month) || empty($year)) {
            $date = $this->getLatestRecordDate();

            if(is_null($date)) {
                $date = Carbon::now();
                $month = $date->format('F');
                $year = $date->format('Y');
            } else {
                $date = Carbon::createFromFormat('d/m/Y H:i', $date)->format('F, Y');

                $month = trim(explode(',', $date)[0]);
                $year = trim(explode(',', $date)[1]);
            }

            return redirect()->route('employee.dtr', [
                'month' => $month,
                'year' => $year
            ]);
    
        }

        $validMonths = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        if(empty($month) || empty($year) || !in_array($month, $validMonths, true)) {
            return redirect()->route('employee.dtr');
        }

        return view('employee.daily-time-record', [
            'title' => 'ESS | My Daily Time Record',
            'header' => 'Daily Time Record',
            'sub' => 'My Daily Time Record',
            'month' => $month,
            'year' => $year,
            'employee_no' => $employee_no
        ]);        
    }

    private function getLatestRecordDate() {
        $latestRecord = EmployeeTimelogs::orderBy('logdatetime', 'desc')->first();
        if(is_null($latestRecord)) {
            return null;
        }

        return $latestRecord->logdatetime;
    }
}
