<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestStatusController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read employee-messages')->only('index');
    }

    public function index() {
        return view('employee.request-status', [
            'action' => 'index',
            'title' => 'ESS | Request Status',
            'header' => 'Request Status | Talk HR',
            'sub' => 'Request any status or message any concerns.'
        ]);
    }
}
