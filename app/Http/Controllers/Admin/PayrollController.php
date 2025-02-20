<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index() {
        return view('admin.payroll.index');
    }

    public function show(string $month, string $year) {

        if(is_null($month) || is_null($year)) {
            return redirect()
                ->route('payroll.index');
        }

        return view('admin.payroll.show', compact('month', 'year'));
    }
}
