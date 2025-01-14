<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email', [
            'title' => 'Password Recovery'
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email input
        $request->validate([
            'email' => 'required|email|exists:employee_account,email',
        ], [
            'email.exists' => 'The email provided does not exists.'
        ]);
        
        // // Attempt to send the reset link to the user's email
        $status = Password::broker('employees')->sendResetLink(
            $request->only('email')
        );

        // Check the status and respond accordingly
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __('auth.' . $status))
            : back()->withErrors(['email' => __('auth.' . $status)]);
    }

}
