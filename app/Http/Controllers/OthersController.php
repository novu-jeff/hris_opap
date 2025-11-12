<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OthersController extends Controller
{
    public function overtime() {
        return view('admin.others.overtime');
    }
}
