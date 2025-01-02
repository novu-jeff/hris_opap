<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read sections')->only('index');
        $this->middleware('permission:write sections')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.section.index');
    }

    public function create()
    {
        return view('admin.settings.hris.section.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.section.edit', compact('id'));
    }
    
}
