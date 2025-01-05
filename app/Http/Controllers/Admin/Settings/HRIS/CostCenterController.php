<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read cost-center')->only('index');
        $this->middleware('permission:write cost-center')->only(['create', 'edit']);
    }
    
    public function index()
    {
        return view('admin.settings.hris.cost-center.index');
    }

    public function create()
    {
        return view('admin.settings.hris.cost-center.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.cost-center.edit', compact('id'));
    }


}
