<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read leave')->only('index');
        $this->middleware('permission:write leave')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.leave.index');
    }

    public function create()
    {
        return view('admin.settings.hris.leave.create');
    }
    
    public function edit(int $id)
    {
        return view('admin.settings.hris.leave.edit', compact('id'));
    }
    
}
