<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Carl Llemos', 
                'username' => 'superadmin01',
                'role' => 'superadmin',
                'email' => 'superadmin@hris.com', 
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Kim Mariano', 
                'username' => 'admin01',
                'role' => 'admin',
                'email' => 'admin@hris.com', 
                'password' => Hash::make('password')
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrCreate(
                [ 'email' => $admin['email']], 
                [
                    'name' => $admin['name'],
                    'username' => $admin['username'],
                    'email' => $admin['email'],
                    'password' => $admin['password']
                ]
            );
            $user->assignRole($admin['role']);
        }
    }
}
