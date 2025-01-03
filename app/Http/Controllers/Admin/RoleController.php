<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read roles')->only('index', 'show');
        $this->middleware('permission:write roles')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.user-access.index');
    }

    public function create()
    {
        return view('admin.settings.user-access.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.user-access.edit', compact('id'));
    }

    public function show(int $id)
    {
        return view('admin.settings.user-access.show', compact('id'));
    }
}
