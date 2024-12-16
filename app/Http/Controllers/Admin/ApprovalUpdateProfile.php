<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalUpdateProfile extends Controller
{
    public function index()
    {   
        return view('admin.ess.profile-approval.index');
    }

    public function edit(string $employee_no) {
        return view('admin.ess.profile-approval.edit', ['employee_no' => $employee_no]);
    }
}
