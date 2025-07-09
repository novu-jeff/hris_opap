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
            'title' => 'ESS | Request Timelogs',
            'header' => 'Manage Request Timelogs',
            'sub' => 'Track and monitor your request timelogs.'
        ]);
    }

    public function create()
    {
        return view('employee.time-adjustments', [
            'action' => 'create',
            'title' => 'Apply Request Timelogs',
            'header' => 'Apply Request Timelogs',
            'sub' => 'By proceeding, you\'ll be able to apply for requesting a timelog.'
        ]);

    }

    public function edit(int $id)
    {
        return view('employee.time-adjustments', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Request Timelogs',
            'header' => 'Edit Request Timelogs',
            'sub' => 'Feel free to edit or update your leave application.'
        ]);

    }

    

}
