<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.settings.hris.position.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.settings.hris.position.create');
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(int $id)
    {
        return view('admin.settings.hris.position.edit', compact('id'));
    }


}
