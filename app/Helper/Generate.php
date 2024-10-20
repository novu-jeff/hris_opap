<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

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

    public function password(int $length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return Hash::make($password);
    }

}