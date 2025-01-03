<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read roles')->only('index', 'show');
        $this->middleware('permission:write roles')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.roles.index');
    }

    public function create()
    {
        return view('admin.settings.roles.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.roles.edit', compact('id'));
    }

    public function show(int $id)
    {
        return view('admin.settings.roles.show', compact('id'));
    }
}
