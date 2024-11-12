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
            'email' => 'required|exists:users',
            'password' => 'required',
        ];

        $message = [
            'email.exists' => 'The email provided does not exists.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            return redirect()->route('admin.dashboard');

        } else {
            return redirect()->back()
                ->with(['error' => 'Invalid email or password']);
        }
        
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}