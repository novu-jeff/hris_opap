<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {

         $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
            
        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            $token = null;
        }

        return view('auth.passwords.reset', [
            'title' => 'Change Password',
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        // Validate the form data
        $request->validate([
            'email' => 'required|email|exists:employee_account,email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);

        // Attempt to reset the user's password using the custom broker
        $status = Password::broker('employees')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Hash and set the new password
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // Check the status and redirect accordingly
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('employee.login')
            : back()->withErrors(['password' => __('auth.' . $status)]);
    }

}
