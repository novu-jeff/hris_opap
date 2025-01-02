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
                'name' => 'BlitzDev Superadmin', 
                'username' => 'superadmin01',
                'role' => 'superadmin',
                'email' => 'superadmin@hris.com', 
                'password' => Hash::make('password')
            ],
            [
                'name' => 'BlitzDev Admin', 
                'username' => 'admin01',
                'role' => 'admin',
                'email' => 'admin@hris.com', 
                'password' => Hash::make('password')
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrCreate(
                ['name' => $admin['name'], 'email' => $admin['email'], 'email' => $admin['password']], 
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
