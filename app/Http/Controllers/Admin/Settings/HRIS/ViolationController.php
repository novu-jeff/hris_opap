<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read violations')->only('index');
        $this->middleware('permission:write violations')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.violation.index');
    }

    public function create()
    {
        return view('admin.settings.hris.violation.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.violation.edit', compact('id'));
    }
}
