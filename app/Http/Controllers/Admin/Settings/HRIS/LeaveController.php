<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read leave-types')->only('index');
        $this->middleware('permission:write leave-types')->only(['create', 'edit']);
        $this->middleware('permission:read leave-credits')->only(['show']);
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

    public function show(Request $request, int $id)
    {
        $action = $request->action ?? '';
        $employee = $request->employee ?? '';
        
        return view('admin.settings.hris.leave.show', compact('id', 'action', 'employee'));

    }
    
}
