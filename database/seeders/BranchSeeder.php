<?php

namespace Database\Seeders;

use App\Models\Branches;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            ['name' => 'Branch 1', 'code' => 'BCH-1'],
            ['name' => 'Branch 2', 'code' => 'BCH-2'],
        ];

        foreach ($branches as $branch) {
            Branches::updateOrCreate(
                ['name' => $branch['name'], 'code' => $branch['code']], 
                $branch
            );
        }
    }
}
