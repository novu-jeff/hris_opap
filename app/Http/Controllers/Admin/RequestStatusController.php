<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestStatusController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read messages')->only('index');
    }

    public function index(?string $employee_no = null)
    {

        if(is_null($employee_no)) {
            return view('admin.ess.request-status.index', [
                'employee_no' => $employee_no,
                'action' => 'view',
                'title' => 'Employee ChatBox',
                'header' => 'Send Message',
                'sub' => 'View all messages of employees.'
            ]);
        }

        return view('admin.ess.request-status.index', [
            'employee_no' => $employee_no,
            'action' => 'send',
            'title' => 'Send Message | ' . $employee_no,
            'header' => 'Send Message',
            'sub' => 'View all messages of employees.'
        ]);
        
    }
}
