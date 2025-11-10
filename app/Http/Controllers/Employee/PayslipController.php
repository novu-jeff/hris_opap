<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class PayslipController extends Controller
{
    public function index() {
        return view('employee.payslip', [
            'action' => 'index',
            'title' => 'Payslip',
            'header' => 'Monitor your salary',
        ]);
    }
}
