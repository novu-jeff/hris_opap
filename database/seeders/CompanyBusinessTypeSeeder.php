<?php

namespace Database\Seeders;

use App\Models\CompanyBusinessType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyBusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Retail'],
            ['name' => 'Wholesale'],
            ['name' => 'Service'],
            ['name' => 'Manufacturing'],
            ['name' => 'Technology'],
            ['name' => 'Construction'],
            ['name' => 'Healthcare'],
            ['name' => 'Education'],
            ['name' => 'Hospitality'],
            ['name' => 'Government'] 
        ];

        foreach ($types as $type) {
            CompanyBusinessType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
