<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index() {
        return view('auth.home.login');
    }

    public function logout() {
        Auth::guard('applicant')->logout();
        return redirect()->route('home.login');
    }
}