<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayslipRequestController extends Controller
{
    public function index(Request $request) {
        $status = $request->status ?? 'pending';
        return view('admin.ess.payslip-request.index', compact('status'));
    }
}
