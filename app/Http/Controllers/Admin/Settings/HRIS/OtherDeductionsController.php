<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtherDeductionsController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read other-deductions')->only('index');
        $this->middleware('permission:write other-deductions')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.deductions.index');
    }

    public function create()
    {
        return view('admin.settings.hris.deductions.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.deductions.edit', compact('id'));
    }
}
