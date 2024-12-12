<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessSlipController extends Controller
{
    public function index()
    {
        return view('employee.business-slip', [
            'action' => 'view',
            'title' => 'ESS | Business Slip Applications',
            'header' => 'Manage Business Slip',
            'sub' => 'Track and monitor youe employment records.'
        ]);
    }

    public function create()
    {
        return view('employee.business-slip', [
            'action' => 'create',
            'title' => 'Apply Business Slip',
            'header' => 'Business Slip Application',
            'sub' => 'By proceeding, you\'ll be able to apply for a Business Slip.'
        ]);

    }

    public function edit(int $id)
    {
        return view('employee.business-slip', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Business Slip',
            'header' => 'Edit Application',
            'sub' => 'Feel free to edit or update your Business Slip application.'
        ]);

    }
}
