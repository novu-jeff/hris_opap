<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestStatusController extends Controller
{
    public function index() {
        return view('employee.request-status', [
            'action' => 'index',
            'title' => 'Request Status',
            'header' => 'Request Status | Talk HR',
            'sub' => 'Request any status or message any concerns.'
        ]);
    }
}
