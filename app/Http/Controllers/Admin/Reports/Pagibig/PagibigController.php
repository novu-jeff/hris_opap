<?php

namespace App\Http\Controllers\Admin\Reports\Pagibig;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagibigController extends Controller
{
    public function index()
    {
        return view('admin.reports.pagibig.index');
    }
}
