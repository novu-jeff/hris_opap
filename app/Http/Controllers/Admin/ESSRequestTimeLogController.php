<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ESSRequestTimeLogController extends Controller
{
    public function __construct() {
        $this->middleware('permission:read request-log')->only('index');
    }

    public function index(Request $request)
    {
        $status = $request->status ?? 'pending';

        return view('admin.ess.request-timelog.index', compact('status'));
    }
}
