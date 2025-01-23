<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    public function __construct() {
        $this->middleware('permission:read scheduler')->only('index');
    }

    public function index()
    {
        return view('admin.settings.scheduler');
    }
}
