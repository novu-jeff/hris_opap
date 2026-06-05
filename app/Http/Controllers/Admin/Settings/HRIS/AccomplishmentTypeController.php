<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccomplishmentTypeController extends Controller
{
   
    public function __construct() {
        //$this->middleware('permission:read employment-type')->only('index');
        //$this->middleware('permission:write employment-type')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.accomplishment-type.index');
    }

    public function create()
    {
        return view('admin.settings.hris.accomplishment-type.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.accomplishment-type.edit', compact('id'));
    }


}
