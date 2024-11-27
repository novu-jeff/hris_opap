<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use App\Models\OtherDeductions;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(int $id)
    {
        return view('admin.settings.hris.emp-deductions.create', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(int $id)
    {
        return view('admin.settings.hris.emp-deductions.edit', compact('id'));
    }
}
