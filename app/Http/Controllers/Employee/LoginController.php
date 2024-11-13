<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index() {
        return view('auth.employee.login');
    }

    public function store(Request $request) {
        
        $rules = [
            'email' => 'required|exists:employee_account',
            'password' => 'required',
        ];

        $message = [
            'email.exists' => 'The email provided does not exists.',
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        if(Auth::guard('employee')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            return redirect()->route('employee.dashboard');

        } else {
            return redirect()->back()
                ->with(['error' => 'Invalid email or password']);
        }
        
    }

    public function logout() {
        Auth::guard('employee')->logout();
        return redirect()->route('employee.login');
    }
}