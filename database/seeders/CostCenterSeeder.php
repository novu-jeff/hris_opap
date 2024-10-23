<?php

namespace Database\Seeders;

use App\Models\CostCenters;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cost_centers = [
            ['name' => 'Cost Center 1', 'code' => 'CC-1'],
            ['name' => 'Cost Center 2', 'code' => 'CC-2'],
        ];

        foreach ($cost_centers as $cost_center) {
            CostCenters::updateOrCreate(
                ['name' => $cost_center['name'], 'code' => $cost_center['code']], 
                $cost_center
            );
        }
    }
}
