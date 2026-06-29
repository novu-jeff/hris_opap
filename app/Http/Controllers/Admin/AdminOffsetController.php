<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminOffsetController extends Controller
{
    public function index()
    {
        $status = request('status', 'pending');
        return view('admin.ess.offset.index', compact('status'));
    }

    public function show($id)
    {
        return view('admin.ess.offset.show', compact('id'));
    }
}