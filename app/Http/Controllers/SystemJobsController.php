<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemJobsController extends Controller
{
    public function index(Request $request)
    {

        $batch_id = $request->id ?? null;

        return view('admin.system.jobs', compact('batch_id'));
    }
}
