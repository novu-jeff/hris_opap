<?php

namespace App\Helper;

use App\Models\ApplicantUsers;
use App\Models\EmployeeAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Str;

class Generate {

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
        while (EmployeeAccount::where('email', $email)->exists()) {
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