<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestStatusController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read messages')->only('index');
    }

    public function index(string $employee_no = null)
    {

        if(is_null($employee_no)) {
            return view('admin.ess.request-status.index', [
                'employee_no' => $employee_no,
                'action' => 'view',
                'title' => 'Request Status',
                'header' => 'Manage Request Status | Messages',
                'sub' => 'View all request status and concerns of employees.'
            ]);
        }

        return view('admin.ess.request-status.index', [
            'employee_no' => $employee_no,
            'action' => 'send',
            'title' => 'Request Status',
            'header' => 'Manage Request Status | Messages',
            'sub' => 'View all request status and concerns of employees.'
        ]);
        
    }
}
