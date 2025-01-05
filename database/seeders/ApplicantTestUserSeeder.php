<?php

namespace Database\Seeders;

use App\Models\ApplicantUsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApplicantTestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicantUsers = [
            [
                'image' => null,
                'firstname' => 'John',
                'middlename' => 'A.',
                'lastname' => 'Doe',
                'phone_no' => '09171234567',
                'tel_no' => '02-1234567',
                'sex' => 'Male',
                'birthday' => '1990-01-01',
                'civil_status' => 'Single',
                'address' => '123 Main St',
                'province' => 'Metro Manila',
                'city' => 'Quezon City',
                'resume' => null,
                'email' => 'applicant01@test.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => null,
                'firstname' => 'Jane',
                'middlename' => null,
                'lastname' => 'Smith',
                'phone_no' => '09181234567',
                'tel_no' => null,
                'sex' => 'Female',
                'birthday' => '1995-05-15',
                'civil_status' => 'Married',
                'address' => '456 Elm St',
                'province' => 'Laguna',
                'city' => 'San Pablo City',
                'resume' => null,
                'email' => 'applicant02@test.com',
                'password' => Hash::make('password'),
                'level' => 'Master',
                'school_name' => 'Ateneo de Manila University',
                'course' => 'Information Systems',
                'started' => '2015',
                'finished' => '2017',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => null,
                'firstname' => 'Carlos',
                'middlename' => 'M.',
                'lastname' => 'Reyes',
                'phone_no' => '09191234567',
                'tel_no' => '045-9876543',
                'sex' => 'Male',
                'birthday' => '1988-03-10',
                'civil_status' => 'Single',
                'address' => '789 Pine St',
                'province' => 'Cebu',
                'city' => 'Cebu City',
                'resume' => null,
                'email' => 'applicant03@test.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($applicantUsers as $user) {
            ApplicantUsers::updateOrCreate(
                ['email' => $user['email']], 
                $user 
            );
        }

        
    }
}
