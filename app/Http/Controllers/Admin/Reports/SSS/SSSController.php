<?php

namespace App\Http\Controllers\Admin\Reports\SSS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SSSController extends Controller
{
    public function index()
    {
        return view('admin.reports.sss.index');
    }
}
