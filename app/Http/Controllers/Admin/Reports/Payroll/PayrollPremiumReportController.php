<?php

namespace App\Http\Controllers\Admin\Reports\Payroll;

use App\Http\Controllers\Controller;
use App\Models\BonusItemsPayroll;
use Illuminate\Http\Request;

class PayrollPremiumReportController extends Controller
{
    /**
     * Show payroll report
     */
    public function index(Request $request)
    {
        // Filters
   

        return view('admin.reports.payroll.premium.index');
    }


    public function view(int $payrollId)
    {
        // Filters
   

        return view('admin.reports.payroll.premium.view', compact('payrollId'));
    }
}
