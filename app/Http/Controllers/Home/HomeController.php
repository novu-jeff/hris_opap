<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request) {

        $parameter = $request->get('search') ?? '';

        return view('home.index', compact('parameter'));
    }
}