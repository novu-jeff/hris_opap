<?php

namespace Database\Seeders;

use App\Models\JobPosts;
use Illuminate\Database\Seeder;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'position' => 'Software Engineer',
                'company_name' => 'Tech Innovators Inc.',
                'location' => 'New York, USA',
                'setup' => 'Remote',
                'type' => 'Full-time',
                'min_salary' => 60000,
                'max_salary' => 80000,
                'description' => 'Develop and maintain web applications using modern technologies.',
                'slots' => 5,
            ],
            [
                'position' => 'Frontend Developer',
                'company_name' => 'Creative Minds Co.',
                'location' => 'San Francisco, USA',
                'setup' => 'Hybrid',
                'type' => 'Part-time',
                'min_salary' => 40000,
                'max_salary' => 55000,
                'description' => 'Work on designing and implementing UI/UX for web applications.',
                'slots' => 3,
            ],
            [
                'position' => 'Data Scientist',
                'company_name' => 'DataWorks Ltd.',
                'location' => 'Toronto, Canada',
                'setup' => 'On-site',
                'type' => 'Contract',
                'min_salary' => 70000,
                'max_salary' => 90000,
                'description' => 'Analyze and interpret complex data to help drive decision-making.',
                'slots' => 2,
            ],
        ];

        foreach ($data as $item) {
            JobPosts::updateOrCreate(
                [
                    'position' => $item['position'], 
                    'company_name' => $item['company_name'], 
                    'location' => $item['location']
                ],
                [
                    'setup' => $item['setup'],
                    'type' => $item['type'],
                    'min_salary' => $item['min_salary'],
                    'max_salary' => $item['max_salary'],
                    'description' => $item['description'],
                    'slots' => $item['slots'],
                ]
            );
        }
    }
}
