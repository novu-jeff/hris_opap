<?php

namespace Database\Seeders;

use App\Models\Positions;
use App\Models\Violations;
use Illuminate\Database\Seeder;

class ViolationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $violations = [
            ['name' => 'Late Arrival'],
            ['name' => 'Absence Without Notice'],
            ['name' => 'Failure to Meet Deadlines'],
            ['name' => 'Misuse of Company Resources'],
            ['name' => 'Violation of Safety Rules'],
            ['name' => 'Insubordination'],
            ['name' => 'Unprofessional Behavior'],
            ['name' => 'Data Privacy Breach'],
            ['name' => 'Excessive Breaks'],
            ['name' => 'Harassment'],
        ];

        foreach ($violations as $violation) {
            Violations::updateOrCreate(
                ['name' => $violation['name']], 
                $violation
            );
        }
    }
}
