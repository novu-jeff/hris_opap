<?php

namespace App\Http\Controllers\Admin\Reports\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollEmeRataItems;
use Illuminate\Http\Request;

class PayrollEmeRataReportController extends Controller
{
    /**
     * Show payroll report
     */
    public function index(Request $request)
    {
        // Filters
   

        return view('admin.reports.payroll.eme-rata.index');
    }


    public function view(int $payrollId)
    {
        // Filters
   

        return view('admin.reports.payroll.eme-rata.view', compact('payrollId'));
    }
}
