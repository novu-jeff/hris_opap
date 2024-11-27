<?php

namespace Database\Seeders;

use App\Models\Positions;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $positions = [
            ['code' => 'SWE', 'name' => 'Software Engineer'],
            ['code' => 'PM', 'name' => 'Project Manager'],
            ['code' => 'DA', 'name' => 'Data Analyst'],
            ['code' => 'SYSADM', 'name' => 'System Administrator'],
            ['code' => 'UIUX', 'name' => 'UI/UX Designer'],
        ];

        foreach ($positions as $position) {
            Positions::updateOrCreate(
                ['code' => $position['code'], 'name' => $position['name']], 
                $position
            );
        }
    }
}
