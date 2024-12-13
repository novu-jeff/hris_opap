<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeDailyTimeRecordController extends Controller
{
    public function index()
    {
        return view('employee.daily-time-record', [
            'title' => 'ESS | My Daily Time Record',
            'header' => 'Daily Time Record',
            'sub' => 'My Daily Time Record'
        ]);        
    }
}
