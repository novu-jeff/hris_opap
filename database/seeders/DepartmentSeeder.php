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
            ['code' => 'DEPT1', 'name' => 'Department 1', 'cost_center_id' => 1],
            ['code' => 'DEPT1', 'name' => 'Department 2', 'cost_center_id' => 1],
        ];
    
        foreach ($departments as $department) {
            Departments::updateOrCreate(
                ['code' => $department['code'], 'name' => $department['name'], 'cost_center_id' => $department['cost_center_id']], 
                $department
            );
        }
    }
}
