<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestStatusController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read request-status')->only('index');
    }

    public function index(int $id = null)
    {
        return view('admin.ess.request-status.index', [
            'id' => $id,
            'action' => 'view',
            'title' => 'Request Status',
            'header' => 'Manage Request Status | Messages',
            'sub' => 'View all request status and concerns of employees.'
        ]);
    }
}
