<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimeAdjustmentsController extends Controller
{
    public function __construct() {
        $this->middleware('permission:read apply-time-adjustments')->only('index');
        $this->middleware('permission:write apply-time-adjustments')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('employee.time-adjustments', [
            'action' => 'view',
            'title' => 'ESS | Time Adjustments',
            'header' => 'Manage Time Adjustments',
            'sub' => 'Track and monitor your time adjustments.'
        ]);
    }

    public function create()
    {
        return view('employee.time-adjustments', [
            'action' => 'create',
            'title' => 'Apply Time Adjustments',
            'header' => 'Apply Time Adjustments',
            'sub' => 'By proceeding, you\'ll be able to apply for requesting time adjustments.'
        ]);

    }

    public function edit(int $id)
    {
        return view('employee.time-adjustments', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Time Adjustments',
            'header' => 'Edit Time Adjustments',
            'sub' => 'Feel free to edit or update your time adjustments.'
        ]);

    }

    

}
