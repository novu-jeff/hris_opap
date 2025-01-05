<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use App\Models\OtherDeductions;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read employee-deductions')->only('index');
        $this->middleware('permission:write employee-deductions')->only(['create', 'edit']);
    }

    public function index(int $id)
    {

        $header = OtherDeductions::where('id', $id)
            ->where('source', 'entry')
            ->first();

        if(!$header) {
            return redirect()->route('other-deductions.index');
        }

        return view('admin.settings.hris.emp-deductions.index', compact('id', 'header'));
    }

    public function create(int $id)
    {
        return view('admin.settings.hris.emp-deductions.create', compact('id'));
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.emp-deductions.edit', compact('id'));
    }
}
