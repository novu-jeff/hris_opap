<?php

namespace App\Helper;

use App\Models\ApplicantUsers;
use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Str;

class Generate {

    public function employee_no() {
        $lastEmployeeNo = EmployeeInformation::where('employee_no', 'like', 'RCRT-%')
                            ->max('employee_no');
    
        if (!$lastEmployeeNo) {
            $employee_no = 1;
        } else {
            $employee_no = (int) str_replace('RCRT-', '', $lastEmployeeNo) + 1;
        }
    
        $employeeNo = 'RCRT-' . str_pad($employee_no, 2, '0', STR_PAD_LEFT);
    
        while (EmployeeInformation::where('employee_no', $employeeNo)->exists()) {
            $employee_no++;
            $employeeNo = 'RCRT-' . str_pad($employee_no, 2, '0', STR_PAD_LEFT);
        }
    
        return $employeeNo;
    }
    

    public function code(string $prefix, int $length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomPart = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPart .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $prefix . $randomPart;
    }

    public function biometrics() {
        $date = Carbon::now();
        return $date->format('Ymd') . $date->timestamp;
    }

    public function email(string $employee_no, string $firstname, string $lastname) {
        $suffix = trim(env('COMPANY_DOMAIN'));
        $emailPrefix = strtolower(trim(str_replace(' ', '.', "{$firstname}.{$lastname}")));
        
        // Start with the basic format
        $email = "{$emailPrefix}@{$suffix}";
        $counter = 1;
    
        // Check if the email already exists
        while (EmployeeAccount::where('email_id', $email)->exists()) {
            // If it exists, append a counter to make it unique
            $email = "{$emailPrefix}.{$counter}@{$suffix}";
            $counter++;
        }
    
        return $email;
    }

    public function password(int $length = 8, int $cost = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = ''; 
    
        // Generate random password
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
    
        // Hash the password with a custom cost
        $hashedPassword = Hash::make($password, [
            'cost' => $cost,  // Set the cost parameter
        ]);
    
        return [
            'plain' => $password,
            'hashed' => $hashedPassword,
        ];
    }

}