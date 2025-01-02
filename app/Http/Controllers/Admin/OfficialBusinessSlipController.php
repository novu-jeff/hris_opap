<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OfficialBusinessSlipController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read obs')->only('index');
    }

    public function index(Request $request)
    {
        $status = $request->status ?? 'pending';

        return view('admin.ess.business-slip.index', compact('status'));
    }
}
