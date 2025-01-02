<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PositionController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read positions')->only('index');
        $this->middleware('permission:write positions')->only(['create', 'edit']);
    }
    
    public function index()
    {
        return view('admin.settings.hris.position.index');
    }

    public function create()
    {
        return view('admin.settings.hris.position.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.position.edit', compact('id'));
    }


}
