<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read leave-types')->only('index');
        $this->middleware('permission:write leave-types')->only(['create', 'edit']);
    }

    public function index(Request $request) {

        $status = $request->status ?? 'pending';

        return view('admin.ess.leave.index', compact('status'));
    }
}