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
    
        $date = "{$month}, {$year}";
    
        return view('admin.reports.daily-time-record.employee.index', compact('date'));
    }
    
}
