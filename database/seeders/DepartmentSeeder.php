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
            ['code' => 'P1', 'name' => 'Program 1', 'description' => 'GPH - MILF Peace Process'],
            ['code' => 'P2', 'name' => 'Program 2', 'description' => 'GPH - MNLF Peace Process'],
            ['code' => 'P3', 'name' => 'Program 3', 'description' => 'Localized Peace Engagement'],
            ['code' => 'P4', 'name' => 'Program 4', 'description' => 'GPH - RPM-P / RPA / ABB / CBA-CPLA Peace Process'],
            ['code' => 'P5', 'name' => 'Program 5', 'description' => 'Social Healing and Peacebuilding'],
            ['code' => 'P6', 'name' => 'Program 6', 'description' => 'PAyapa at MAsaganang PamayaNAn (PAMANA) Program'],
            ['code' => 'P7', 'name' => 'Program 7', 'description' => 'Internal Cooperation and Partnership'],
            ['code' => 'P8', 'name' => 'Program 8', 'description' => 'Human Capital / Organization Capital / Finance and Resources / Strategic Communications'],
            ['code' => 'EO', 'name' => 'Executive Offices', 'description' => 'Executive Office']
        ];

        foreach ($departments as $department) {
            Departments::updateOrCreate(
                ['code' => $department['code'], 'name' => $department['name']],
                $department
            );
        }
    }
}
