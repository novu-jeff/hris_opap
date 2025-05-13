<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
