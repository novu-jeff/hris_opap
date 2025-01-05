<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HRISController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read hris')->only('index', 'manual');
    }

    public function index()
    {
        return view('admin.hris.index');
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
