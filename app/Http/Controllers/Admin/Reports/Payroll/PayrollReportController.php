<?php

namespace App\Http\Controllers\Admin\Reports\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read payroll-report');
    }

    /**
     * Show payroll report
     */
    public function index(Request $request)
    {
        // Filters
   

        return view('admin.reports.payroll.index');
    }


    public function view(int $payrollId)
    {
        // Filters
   

        return view('admin.reports.payroll.view', compact('payrollId'));
    }
}
