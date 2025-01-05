<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read departments')->only('index');
        $this->middleware('permission:write departments')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.department.index');
    }

    public function create()
    {
        return view('admin.settings.hris.department.create');
    }
    
    public function edit(int $id)
    {
        return view('admin.settings.hris.department.edit', compact('id'));
    }
    
}
