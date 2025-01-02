<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
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
