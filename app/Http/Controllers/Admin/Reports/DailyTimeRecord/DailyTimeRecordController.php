<?php

namespace App\Http\Controllers\Admin\Reports\DailyTimeRecord;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTimelogs;
use App\Models\EmployeeInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyTimeRecordController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        return view('admin.reports.daily-time-record.index', compact('type'));
    }

    public function show(Request $request, string $employee_no)
    {

        $month = $request->input('month'); 
        $year = $request->input('year');
        
        if(empty($employee_no) || !$this->isValidEmployee($employee_no)) {
            return redirect()->route('reports.dtr');
        }

        if(empty($month) || empty($year)) {
            $date = $this->getLatestRecordDate();

            if(is_null($date)) {
                $date = Carbon::now();
                $month = $date->format('F');
                $year = $date->format('Y');
            } else {

                $date = Carbon::parse($date)->format('F, Y');

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
        $latestRecord = EmployeeTimelogs::orderBy('timestamp', 'desc')->first();
        
        if(is_null($latestRecord)) {
            return null;
        }

        return $latestRecord->timestamp;
    }

    private function isValidEmployee(string $employee_no)
    {
        $bsd_emp_identical = config('app.bsd_emp_identical');

        if (in_array($employee_no, $bsd_emp_identical)) {
            return EmployeeInformation::where('employee_no', $employee_no)->exists();
        }

        return EmployeeInformation::where('employee_no', $employee_no)
            ->whereNotNull('bsd_no')
            ->exists();
    }


}
