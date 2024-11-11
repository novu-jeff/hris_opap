<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClockInOutController extends Controller
{
    public function index()
    {
        return view('admin.ess.clock-in-out.index', [
            'action' => 'view',
            'title' => 'Clock In and Out',
            'header' => 'Clock In and Out',
            'sub' => 'Track employee\'s clock in and clock out'
        ]);
    }
}
