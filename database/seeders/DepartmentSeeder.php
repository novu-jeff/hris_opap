<?php

namespace Database\Seeders;

use App\Models\Departments;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['code' => 'P1', 'name' => 'Program 1'],
            ['code' => 'P2', 'name' => 'Program 2'],
            ['code' => 'P3', 'name' => 'Program 3'],
            ['code' => 'P4', 'name' => 'Program 4'],
            ['code' => 'P5', 'name' => 'Program 5'],
            ['code' => 'P6', 'name' => 'Program 6'],
            ['code' => 'P7', 'name' => 'Program 7'],
            ['code' => 'P8', 'name' => 'Program 8'],
            ['code' => 'EO', 'name' => 'Executive Offices']
        ];

        foreach ($departments as $department) {
            Departments::updateOrCreate(
                ['code' => $department['code'], 'name' => $department['name']],
                $department
            );
        }
    }
}
