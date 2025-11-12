<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalUpdateProfile extends Controller
{

    public function __construct() {
        $this->middleware('permission:read employee-profile-approval')->only('index');
        $this->middleware('permission:write employee-profile-approval')->only(['create', 'edit']);
    }

    public function index()
    {   
        return view('admin.ess.profile-approval.index');
    }

    public function show(string $employee_no, string $form) {
        
        $allowed = [
            'personal', 'education', 'family',
            'children', 'employment-history', 'civil-service',
            'trainings', 'other-works', 'skills'
        ];

        if(!in_array( $form, $allowed)) {
            return redirect()->route('ess.approval-profile.index');
        }

        return view('admin.ess.profile-approval.show', ['employee_no' => $employee_no, 'form' => $form]);
    }
}
