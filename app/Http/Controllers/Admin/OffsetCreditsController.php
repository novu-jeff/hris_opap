<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OffsetCreditsController extends Controller
{
    public function __construct()
    {
        /*$this->middleware('permission:read offset-credits')
            ->only('index');

        $this->middleware('permission:write offset-credits')
            ->only(['create','edit']);*/
    }

    public function index()
    {
        return view('admin.ess.offset-credits.index',[
            'action'=>'view',
            'title'=>'Offset Credits',
            'header'=>'Offset Credits',
            'sub'=>'Manage employee offset credits.'
        ]);
    }

    public function create()
    {
        return view('admin.ess.offset-credits.index',[
            'action'=>'create',
            'title'=>'Add Offset Credit',
            'header'=>'Add Offset Credit',
            'sub'=>'Grant manual offset credits.'
        ]);
    }

    public function edit($id)
    {
        return view('admin.ess.offset-credits.index',[
            'id'=>$id,
            'action'=>'edit',
            'title'=>'Edit Offset Credit',
            'header'=>'Edit Offset Credit',
            'sub'=>'Update offset credit.'
        ]);
    }
}