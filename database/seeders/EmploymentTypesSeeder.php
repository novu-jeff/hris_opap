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

        $product = config('app.product');

        if($product == 'government') {
            $data = [
                ['name' => 'Contractual', 'code' => 'RC'], 
                ['name' => 'Contract of Service', 'code' => 'COS'],
                ['name' => 'Job Order', 'code' => 'JO'],
            ];
        } else {
            $data = [];
        }

        foreach ($data as $item) {
            EmployementTypes::updateOrCreate(
                ['name' => $item['name']],
                $item 
            );
        }
    }
}
