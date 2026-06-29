<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeOffsetRequest;

class OffsetController extends Controller
{
    public function __construct()
    {
       // $this->middleware('permission:read apply-offset')->only('index');
       // $this->middleware('permission:write apply-offset')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('employee.offset', [
            'action' => 'view',
            'title'  => 'ESS | Authority to Render Offsetting',
            'header' => 'Authority to Render Offsetting',
            'sub'    => 'Track and monitor your offset applications.'
        ]);
    }

    public function create()
    {
        return view('employee.offset', [
            'action' => 'create',
            'title'  => 'ESS | Authority to Render Offsetting',
            'header' => 'Apply for Authority to Render Offsetting',
            'sub'    => 'Use your earned overtime credits to request offsetting.'
        ]);
    }

    public function edit(int $id)
    {
        $record = EmployeeOffsetRequest::findOrFail($id);

        if ($record->status == 'pending') {
            $action = 'Edit';
        } else {
            $action = 'View';
        }

        return view('employee.offset', [
            'id'      => $id,
            'action'  => $action,
            'status'  => $record->status,
            'title'   => $action . ' | Authority to Render Offsetting',
            'header'  => $action . ' Authority to Render Offsetting',
            'sub'     => '',
        ]);
    }
}