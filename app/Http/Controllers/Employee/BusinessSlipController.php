<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessSlipController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read apply-obs')->only('index');
        $this->middleware('permission:write apply-obs')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('employee.business-slip', [
            'action' => 'view',
            'title' => 'ESS | Business Slip Applications',
            'header' => 'Manage Business Applications',
            'sub' => 'Track and monitor your OB applications.'
        ]);
    }

    public function create()
    {
        return view('employee.business-slip', [
            'action' => 'create',
            'title' => 'Apply Business Slip',
            'header' => 'Business Slip Application',
            'sub' => 'By proceeding, you\'ll be able to apply for a business application.'
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
