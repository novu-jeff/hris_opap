<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankInformationController extends Controller
{
   
    public function __construct() {
        $this->middleware('permission:read bank-information')->only('index');
        $this->middleware('permission:write bank-information')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.bank-information.index');
    }
     
    public function create()
    {
        return view('admin.settings.hris.bank-information.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.bank-information.edit', compact('id'));
    }


}
