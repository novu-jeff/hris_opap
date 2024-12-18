<?php

namespace App\Http\Controllers\Admin\Settings\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        return view('admin.settings.payroll.holidays.index');
    }

    public function create()
    {
        return view('admin.settings.payroll.holidays.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.payroll.holidays.edit', compact('id'));
    }
}
