<?php

namespace App\Http\Controllers\Admin\Reports\DailyTimeRecord;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyTimeRecordController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.daily-time-record.index');
    }

    public function show(Request $request, string $employee_no)
    {

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

            return redirect()->route('dtr.show', [
                'id' => $employee_no,
                'month' => $month,
                'year' => $year
            ]);
    
        }

        $validMonths = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        if(empty($month) || empty($year) || !in_array($month, $validMonths, true)) {
            return redirect()->route('reports.dtr');
        }

        return view('admin.reports.daily-time-record.employee.show', compact('employee_no', 'month', 'year'));
    }
    
    private function getLatestRecordDate() {
        $latestRecord = EmployeeTimelogs::orderBy('logdatetime', 'desc')->first();
        if(is_null($latestRecord)) {
            return null;
        }

        return $latestRecord->logdatetime;
    }

}
