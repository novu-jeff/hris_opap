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
            ['name' => 'Software Engineer'],
            ['name' => 'Project Manager'],
            ['name' => 'Data Analyst'],
            ['name' => 'System Administrator'],
            ['name' => 'UI/UX Designer']
        ];

        foreach ($positions as $position) {
            Positions::updateOrCreate(
                ['name' => $position['name']], 
                $position
            );
        }
    }
}
