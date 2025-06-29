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

        $product = config('app.product');

        if($product == 'government') {
            $data = [
                ['code' => 'HO', 'name' => 'Head Office'],
                ['code' => 'FO', 'name' => 'Field Office'],
            ];
        } else {
            $data = [];
        }

        foreach ($data as $data) {
            Branches::updateOrCreate(
                ['name' => $data['name'], 'code' => $data['code']], 
                $data
            );
        }
    }
}
