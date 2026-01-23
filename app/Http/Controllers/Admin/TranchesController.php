<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranchesController extends Controller
{
    public function __construct() {
       // $this->middleware('permission:read tranches')->only(['index', 'show']);
    }

    public function index()
    {
        return view('admin.settings.tranches.index');
    }

    public function create()
    {
        return view('admin.settings.tranches.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.tranches.edit', compact('id'));
    }

}
