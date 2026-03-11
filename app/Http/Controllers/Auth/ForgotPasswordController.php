<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Throwable;

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
            'email.exists' => 'The email provided does not exist.'
        ]);

        // Check if the account is locked
        $employeeAccount = EmployeeAccount::where('email', $request->email)->first();

        if ($employeeAccount && $employeeAccount->isLocked) {
            return back()->withErrors([
                'email' => 'Unable to send reset link because your account is locked.',
            ]);
        }

        try {
            // Attempt to send the reset link to the user's email
            $status = Password::broker('employees')->sendResetLink(
                $request->only('email')
            );
        } catch (Throwable $exception) {
            Log::warning('Password reset email failed to send', [
                'email' => $request->input('email'),
                'broker' => 'employees',
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'Unable to send reset link at the moment. Please try again later.',
            ]);
        }

        // Check the status and respond accordingly
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __('auth.' . $status))
            : back()->withErrors(['email' => __('auth.' . $status)]);
    }

}
