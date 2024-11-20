<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['code' => 'RC', 'name' => 'Regular Contractual'],
            ['code' => 'COS', 'name' => 'Contract of Service'],
            ['code' => 'JO', 'name' => 'Job Offer'],
        ];

        foreach ($categories as $category) {
            JobCategory::updateOrCreate(
                ['code' => $category['code'], 'name' => $category['name']], 
                $category
            );
        }
    }
}
