<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index() {
        return view('auth.employee.login');
    }

    public function store(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }
    
        // Determine identifier: email or employee_no
        $identifier = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email_id' : 'employee_no';
    
        // Fetch the employee account
        $employeeAccount = EmployeeAccount::where($identifier, $request->email)->first();
    
        // Check if account exists and is locked
        if ($employeeAccount && $employeeAccount->isLocked) {
            return redirect()->back()
                ->with(['error' => 'Your account has been locked due to too many failed login attempts. Please contact HR Support.'])
                ->withInput();
        }
    
        if (Auth::guard('employee')->attempt([
            $identifier => $request->email,
            'password' => $request->password
        ])) {
            // Reset login attempts on successful login
            $employeeAccount->login_attempts = 0;
            $employeeAccount->save();
    
            $employee = Auth::guard('employee')->user();
    
            if ($employee->information->status !== 'active') {
                Auth::guard('employee')->logout();
                return redirect()->back()
                    ->with(['error' => 'Oops, your account is currently inactive.'])
                    ->withInput();
            }
    
            return redirect()->route('employee.dashboard');
    
        } else {

            if ($employeeAccount) {
                $employeeAccount->increment('login_attempts');
    
                $remaining = max(0, 5 - $employeeAccount->login_attempts);
    
                if ($employeeAccount->login_attempts >= 5) {
                    $employeeAccount->isLocked = true;
                    $employeeAccount->save();
                    return redirect()->back()
                        ->with(['error' => 'Your account has been locked due to too many failed login attempts. Please contact HR Support.'])
                        ->withInput();
                }
    
                $employeeAccount->save();
    
                $errorMessage = 'Invalid login or password.';
                if ($remaining > 0) {
                    if ($remaining === 1) {
                        $errorMessage .= "<br>One failed attempt remaining, your account will be locked.";
                    } else {
                        $errorMessage .= "<br>You have {$remaining} attempt(s) remaining.";
                    }
                }
    
                return redirect()->back()
                    ->with(['error' => $errorMessage])
                    ->withInput();
            }
    
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