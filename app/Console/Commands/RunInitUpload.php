<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tranche;
use App\Models\TrancheItem;
use App\Models\Position;
use App\Models\Positions;
use App\Models\TrancheItems;

class RunInitUpload extends Command
{
    protected $signature = 'run:initUpload';
    protected $description = 'Import COS, RC tranche CSV files and positions.csv';

    public function handle()
    {
        $this->uploadTranche();
        $this->uploadPositions();

        return Command::SUCCESS;
    }

    protected function uploadTranche()
    {
        $basePath = public_path('templates/defaults');

        $files = [
            ['filename' => 'cos tranche.csv', 'name' => 'COS', 'eligible' => 2],
            ['filename' => 'rc tranche.csv',  'name' => 'Regular Contractual', 'eligible' => 1],
        ];

        foreach ($files as $file) {
            $filePath = $basePath . '/' . $file['filename'];

            if (!file_exists($filePath)) {
                $this->error("Tranche file not found: {$file['filename']}");
                continue;
            }

            $this->info("Processing Tranche: {$file['filename']}");

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

            $this->info("✅ Imported Tranche: {$file['name']}");
        }
    }

    protected function uploadPositions()
    {
        $filePath = public_path('templates/defaults/positions.csv');

        if (!file_exists($filePath)) {
            $this->error("Positions file not found: positions.csv");
            return;
        }

        $this->info("Processing Positions: positions.csv");

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

        $this->info("✅ Imported positions.csv successfully.");
    }

}
