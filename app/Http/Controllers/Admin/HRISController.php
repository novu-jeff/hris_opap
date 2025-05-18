<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HRISController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read hris')->only('index', 'manual');
    }

    public function index(Request $request)
    {
        $employment_type = $request->input('employment_type');
        
        return view('admin.hris.index', compact('employment_type'));
    }

    public function staffing()
    {
        return view('admin.hris.staffing');
    }

    public function show(string $employee_no)
    {
        return view('admin.hris.index', compact('employee_no'));
    }

    public function manual()
    {
        return view('admin.hris.manual');
    }

}
