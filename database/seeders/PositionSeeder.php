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

        $product = config('app.product');

        if($product == 'government') {

            $filePath = public_path('templates/defaults/positions.csv');

            $csv = array_map('str_getcsv', file($filePath));
            $header = array_map('trim', array_shift($csv));

            foreach ($csv as $row) {
                $data = array_combine($header, $row);

                Positions::create([
                    'code'         => $data['Code'] ?? null,
                    'name'      => $data['Position'] ?? null,
                    'salary_grade'  => $data['Salary Grade'] ?? null,
                    'type'          => $data['Type'] ?? null,
                    'w_tax'      => $data['W/Tax'] ?? null,
                ]);
            }
        } else {
            
        }
    }
}
