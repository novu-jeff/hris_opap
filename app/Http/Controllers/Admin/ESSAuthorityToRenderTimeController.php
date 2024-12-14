<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ESSAuthorityToRenderTimeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status ?? 'pending';

        return view('admin.ess.atro.index', compact('status'));
    }
}
