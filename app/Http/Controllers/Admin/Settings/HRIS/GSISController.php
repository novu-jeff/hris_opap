<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GSISController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read gsis-billing')->only('index');
        $this->middleware('permission:write gsis-billing')->only(['create', 'edit']);
    }
   
    public function index()
    {
        return view('admin.settings.hris.gsis.index');
    }

    public function create()
    {
        return view('admin.settings.hris.gsis.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.gsis.edit', compact('id'));
    }
    
}
