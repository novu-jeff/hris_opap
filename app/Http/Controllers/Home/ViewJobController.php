<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewJobController extends Controller
{
    public function index(string $slug) {
        if(is_null($slug)) {
            return redirect()->back();
        }

        return view('home.view-job', compact('slug'));
    }
}