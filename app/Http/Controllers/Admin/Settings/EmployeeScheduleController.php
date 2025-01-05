<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read employee-schedule')->only('index');
        $this->middleware('permission:write employee-schedule')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.employee-schedule.index');
    }

    public function create()
    {
        return view('admin.settings.employee-schedule.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.employee-schedule.edit', compact('id'));
    }
}
