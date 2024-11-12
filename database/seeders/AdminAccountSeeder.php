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
            ['name' => 'Admin', 'email' => 'admin@hris.com', 'password' => Hash::make('password')],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['name' => $admin['name'], 'email' => $admin['email'], 'email' => $admin['password']], 
                $admin
            );
        }
    }
}
