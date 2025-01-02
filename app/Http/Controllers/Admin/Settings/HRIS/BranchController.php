<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read branches')->only('index');
        $this->middleware('permission:write branches')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.branch.index');
    }

    public function create()
    {
        return view('admin.settings.hris.branch.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.branch.edit', compact('id'));
    }


}
