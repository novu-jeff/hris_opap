<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimeAdjustmentsController extends Controller
{
    public function __construct() {
        $this->middleware('permission:read time-adjustments')->only('index');
    }

    public function index(Request $request)
    {
        $status = $request->status ?? 'pending';

        return view('admin.ess.time-adjustments.index', compact('status'));
    }
}
