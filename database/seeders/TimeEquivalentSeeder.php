<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeEquivalentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        // Generate minutes data dynamically
        for ($i = 1; $i <= 45; $i++) {
            $data[] = ['type' => 'minutes', 'time' => $i, 'equiv' => round($i * 0.002, 3)];
        }

        // Generate hours data dynamically
        for ($i = 1; $i <= 8; $i++) {
            $data[] = ['type' => 'hours', 'time' => $i, 'equiv' => round($i * 0.125, 3)];
        }

        for($i = 1; $i <= 30; $i++) {
            $data[] = [
                'type' => 'days',
                'time' => $i,
                'equiv' => round($i * 0.042, 3)
            ];
        }

        // Insert or update records
        foreach ($data as $entry) {
            DB::table('time_equivalents')->updateOrInsert(
                ['type' => $entry['type'], 'time' => $entry['time']],
                ['equiv' => $entry['equiv']]
            );
        }
    }
}
