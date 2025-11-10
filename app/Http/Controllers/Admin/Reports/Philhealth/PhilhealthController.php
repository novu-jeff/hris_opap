<?php

namespace App\Http\Controllers\Admin\Reports\Philhealth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhilhealthController extends Controller
{
    public function index()
    {
        return view('admin.reports.philhealth.index');
    }
}
