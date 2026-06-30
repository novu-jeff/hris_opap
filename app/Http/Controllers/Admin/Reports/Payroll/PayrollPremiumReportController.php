<?php

namespace App\Http\Controllers\Admin\Reports\Payroll;

use App\Http\Controllers\Controller;
use App\Models\BonusItemsPayroll;
use Illuminate\Http\Request;

class PayrollPremiumReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user()->hasPermissionTo('write payroll-report')) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access the Payroll module.');
            }
        
            return $next($request);
        })->only('index');
    }
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
