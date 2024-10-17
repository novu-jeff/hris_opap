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

    public function store(Request $request) {
        
        $rules = [
            'email' => 'required|exists:applicant_users',
            'password' => 'required',
        ];

        $message = [
            'email.exists' => 'The email provided does not exists.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors()
            ];
        }

        if(Auth::guard('applicants')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            return [
                'status' => 'success',
                'message' => 'Login Success!',
                'redirect' => route('home.index')
            ];

        } else {
            return [
                'status' => 'error',
                'title' => 'Login Failed',
                'message' => 'Email or Password is incorrect. Please try again.'
            ];
        }
        
    }

    public function logout() {
        Auth::guard('applicants')->logout();
        return redirect()->route('login');
    }
}