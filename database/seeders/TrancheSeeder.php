<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tranche;
use App\Models\TrancheItems;

class TrancheSeeder extends Seeder
{
    public function run(): void
    {
        
        $basePath = public_path('templates/defaults');

        $files = [
            ['filename' => 'cos tranche.csv', 'name' => 'COS', 'eligible' => 2],
            ['filename' => 'rc tranche.csv',  'name' => 'Regular Contractual', 'eligible' => 1],
        ];

        foreach ($files as $file) {
            $filePath = $basePath . '/' . $file['filename'];

            $tranche = Tranche::create([
                'name' => $file['name'],
                'eligible' => $file['eligible'],
            ]);

            $csv = array_map('str_getcsv', file($filePath));
            $header = array_map('trim', array_shift($csv));

            foreach ($csv as $row) {
                $data = array_combine($header, $row);

                TrancheItems::create([
                    'tranche_id'   => $tranche->id,
                    'salary_grade' => $data['salary_grade'] ?? null,
                    'step_1'       => $data['step_1'] ?? null,
                    'step_2'       => $data['step_2'] ?? null,
                    'step_3'       => $data['step_3'] ?? null,
                    'step_4'       => $data['step_4'] ?? null,
                    'step_5'       => $data['step_5'] ?? null,
                    'step_6'       => $data['step_6'] ?? null,
                    'step_7'       => $data['step_7'] ?? null,
                    'step_8'       => $data['step_8'] ?? null,
                ]);
            }
        }        
    }
}
