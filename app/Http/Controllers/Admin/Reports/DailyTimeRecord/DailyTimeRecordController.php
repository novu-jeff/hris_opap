<?php

namespace App\Http\Controllers\Admin\Reports\DailyTimeRecord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyTimeRecordController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('F')); 
        $year = $request->input('year', now()->format('Y'));
        
        if(empty($month) || empty($year)) {
            return redirect()->route('dtr.index');
        }

        return view('admin.reports.daily-time-record.employee.index', compact('month', 'year'));
    }

    public function show(Request $request, string $employee_no)
    {

        $month = $request->input('month', now()->format('F')); 
        $year = $request->input('year', now()->format('Y'));

        if(empty($month) || empty($year)) {
            return redirect()->route('dtr.index');
        }

        return view('admin.reports.daily-time-record.employee.show', compact('employee_no', 'month', 'year'));
    }
    
}
