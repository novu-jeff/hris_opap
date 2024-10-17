<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\ApplicantUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function index() {
        return view('auth.register');
    }

    public function store(Request $request) {
        
        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'phone_no' => 'required|numeric|unique:applicant_users',
            'sex' => 'required|in:male,female,not to say',
            'birthday' => 'required',
            'civil_status' => 'required|in:single,married,seperated',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'resume' => 'required|mimes:doc,docx,pdf',
            'email' => 'required|email|unique:applicant_users',
            'password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required|min:8'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors()
            ];
        }

        $file = $request->file('resume');
        $extension = $file->getClientOriginalExtension(); 
        $filename = 'applicant_resume_' . time() . '.' . $extension;

        DB::beginTransaction();
        try {
            
            $user = ApplicantUsers::create([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'phone_no' => $request->phone_no,
                'tel_no' => $request->tel_no,
                'sex' => $request->sex,
                'birthday' => $request->birthday,
                'civil_status' => $request->civil_status,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'resume' => $filename,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $file->storeAs('public/applicant/users/'.$user->id, $filename);
            
            DB::commit();

            return [
                'status' => 'success',
                'message' => 'User account `'.$request->email.'` has been created.',
                'redirect' => '_reload'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Error occured: ' . $e->getMessage()
            ];
        }

    }
}