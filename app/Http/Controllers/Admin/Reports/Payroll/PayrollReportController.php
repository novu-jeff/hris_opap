<?php

namespace App\Http\Controllers\Admin\Reports\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryItemsPayroll;
use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    /**
     * Show payroll report
     */
    public function index(Request $request)
    {
        // Filters
   

        return view('admin.reports.payroll.index');
    }
}
