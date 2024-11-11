<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClockInOutController extends Controller
{
    public function index() {
        return view('employee.clock', [
            'action' => 'index',
            'title' => 'Clock In or Out',
            'header' => 'Clock In or Out',
            'sub' => 'Manage your clock in and clock out for work attendance.'
        ]);
    }
}
