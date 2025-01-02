<?php

namespace App\Http\Controllers\Admin\Settings\HRIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtherEarningsController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read other earnings')->only('index');
        $this->middleware('permission:write other earnings')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.settings.hris.earnings.index');
    }

    public function create()
    {
        return view('admin.settings.hris.earnings.create');
    }

    public function edit(int $id)
    {
        return view('admin.settings.hris.earnings.edit', compact('id'));
    }


}
