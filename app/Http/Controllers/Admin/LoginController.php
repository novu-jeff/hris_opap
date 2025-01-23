<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index() {
        return view('auth.admin.login');
    }

    public function login(Request $request) {
        
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        if(Auth::attempt([
            filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $request->email,
            'password' => $request->password
        ])) {

            return redirect()->route('admin.dashboard');

        } else {
            return redirect()->back()
                ->with(['error' => 'Invalid login or password'])
                ->withInput();
        }
        
        
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}