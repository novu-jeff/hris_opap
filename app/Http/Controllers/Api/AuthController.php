<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {

       $credentials = $request->only('email', 'password');

        if(Auth::guard('employee')->attempt($credentials)) {
            $user = Auth::guard('employee')->user();
            $token = $user->createToken('employee_token')->plainTextToken;
            $token = explode('|', $token)[1];
            $user->isLoggedIn = true;
            $user->token = $token;
            $user->save();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'login success',
                'data' => $user,
            ], 200);
        } else{
            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'invalid username or password'
            ], 401);
        }
    }

    public function logout(Request $request) {
        
        $user = Auth::user();
        $user = EmployeeAccount::where('employee_id', $user->id)->first();
        $user->isLoggedIn = false;
        $user->token = null;
        $user->save();
        $user->tokens()->delete();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'logged out successfully'
        ], 200);

    }
}
