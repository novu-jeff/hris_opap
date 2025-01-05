<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftScheduleController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read shift-schedule')->only('index');
        $this->middleware('permission:write shift-schedule')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.shift-schedule.index');
    }

    public function create()
    {
        return view('admin.settings.shift-schedule.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.shift-schedule.edit', compact('id'));
    }
}
