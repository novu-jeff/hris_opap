<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read my-profile')->only('index');
    }

    public function index() {
        return view('employee.profile', [
            'action' => 'view',
            'title' => 'ESS | My Profile',
            'header' => 'Manage my information',
            'sub' => 'Update or modify all my informations.'
        ]);
    }
}
