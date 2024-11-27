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
            ['code' => 'HO', 'name' => 'Head Office'],
            ['code' => 'FO', 'name' => 'Field Office'],
        ];

        foreach ($branches as $branch) {
            Branches::updateOrCreate(
                ['name' => $branch['name'], 'code' => $branch['code']], 
                $branch
            );
        }
    }
}
