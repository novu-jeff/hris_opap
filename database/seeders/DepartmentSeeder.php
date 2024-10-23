<?php

namespace Database\Seeders;

use App\Models\DepartmentCenters;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Department 1', 'cost_center_id' => 1],
            ['name' => 'Department 2', 'cost_center_id' => 1],
        ];
    
        foreach ($departments as $department) {
            DepartmentCenters::updateOrCreate(
                ['name' => $department['name'], 'cost_center_id' => $department['cost_center_id']], // Fixed key
                $department
            );
        }
    }
}
