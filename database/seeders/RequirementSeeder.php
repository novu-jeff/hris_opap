<?php

namespace Database\Seeders;

use App\Models\JobRequirements;
use Illuminate\Database\Seeder;

class RequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'SSS'], 
            ['name' => 'GSIS'],
            ['name' => 'Philhealth'],
            ['name' => 'TIN'],
        ];

        foreach ($data as $item) {
            JobRequirements::updateOrCreate(
                ['name' => $item['name']],
                $item 
            );
        }
    }
}
