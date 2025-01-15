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
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        if(Auth::guard('employee')->attempt([
            filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email_id' : 'employee_no' => $request->email,
            'password' => $request->password
        ])) {

            return redirect()->route('employee.dashboard');

        } else {
            return redirect()->back()
                ->with(['error' => 'Invalid login or password'])
                ->withInput();
        }
        
    }

    public function logout() {
        Auth::guard('employee')->logout();
        return redirect()->route('employee.login');
    }
}