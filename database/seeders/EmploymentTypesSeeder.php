<?php

namespace Database\Seeders;

use App\Models\EmployementTypes;
use Illuminate\Database\Seeder;

class EmploymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Regular Contractual', 'code' => 'RC'], 
            ['name' => 'Contract of Service', 'code' => 'COS'],
            ['name' => 'Job Offer', 'code' => 'JO'],
        ];

        foreach ($data as $item) {
            EmployementTypes::updateOrCreate(
                ['name' => $item['name']],
                $item 
            );
        }
    }
}
