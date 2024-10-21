<?php

namespace Database\Seeders;

use App\Models\Interview;
use App\Models\InterviewItems;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Technical Interview',
                'description' => 'A technical interview assesses a candidate\'s skills through coding challenges, problem-solving, and discussions on technical concepts, focusing on their expertise in the role.',
                'level' => 1,
                'item' => [
                    ['name' => 'What is HTML?'],
                    ['name' => 'Explain MVC framework'],
                ],
            ]
        ];

        foreach ($data as $item) {
            $interview = Interview::updateOrCreate(
                ['name' => $item['name']], 
                [
                    'description' => $item['description'],
                    'level' => $item['level']
                ]
            );

            foreach ($item['item'] as $childItem) {
                InterviewItems::updateOrCreate(
                    [
                        'interview_id' => $interview->id,
                        'name' => $childItem['name'],
                    ],
                    [
                        'name' => $childItem['name'] 
                    ]
                );
            }
        }
    }
}
