<?php

namespace App\Http\Controllers\Employee;
use App\Models\CompanyInformation;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
         $companyInfo = CompanyInformation::with('type')->first();
        return view('employee.dashboard', compact('companyInfo'));
    }
}