<?php

namespace Database\Seeders;

use App\Models\FAQs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'How to create a leave application?',
                'description' => '
                    <p>Yes, you can. Here’s how:</p>
                    <ul>
                        <li>Go to the Leave Application section in Employee Self-Service.</li>
                        <li>Click "Apply."</li>
                        <li>Fill out the required details in the form.</li>
                        <li>Submit your application.</li>
                        <li>Wait for a response from the relevant approver.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'Can I clock in/out while working from home?',
                'description' => '
                    <p>Yes, here are the steps:</p>
                    <ul>
                        <li>Ensure you clock in during your assigned shift schedule.</li>
                        <li>You can take breaks by clocking out and clocking back in.</li>
                        <li>Clock out at the end of your shift.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to apply for authority to render overtime?',
                'description' => '
                    <p>Follow these steps:</p>
                    <ul>
                        <li>Go to the "Authority to Render Overtime" section in Employee Self-Service.</li>
                        <li>Click "Apply."</li>
                        <li>Complete the required details in the form.</li>
                        <li>Submit your request.</li>
                        <li>Await confirmation from the approver.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to view my payslip?',
                'description' => '
                    <p>Here’s how to view your payslip:</p>
                    <ul>
                        <li>Go to the "Payslip" section in Employee Self-Service.</li>
                        <li>Select the appropriate date range to view your payslip.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to talk to HR?',
                'description' => '
                    <p>To talk to HR, follow these steps:</p>
                    <ul>
                        <li>Go to the "Request Status" section in Employee Self-Service.</li>
                        <li>Submit your concern or query.</li>
                        <li>Wait for a response from the HR team.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to apply for an official business slip?',
                'description' => '
                    <p>Here’s how to apply for an official business slip:</p>
                    <ul>
                        <li>Go to the "Official Business Slip" section in Employee Self-Service.</li>
                        <li>Click "Apply."</li>
                        <li>Fill in the required details.</li>
                        <li>Submit your application.</li>
                        <li>Wait for the approver’s response.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to view my DTR?',
                'description' => '
                    <p>Follow these steps to view your DTR:</p>
                    <ul>
                        <li>Navigate to the "DTR" section in Employee Self-Service.</li>
                        <li>Select the desired date range to view your records.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to know employees in every department or section?',
                'description' => '
                    <p>Here’s how you can view employees:</p>
                    <ul>
                        <li>Go to "My Directory" in Employee Self-Service.</li>
                        <li>Browse through the list of employees and their positions in all departments and sections.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to know my co-workers in the same department or section?',
                'description' => '
                    <p>Follow these steps to view your co-workers:</p>
                    <ul>
                        <li>Go to "My Team" in Employee Self-Service.</li>
                        <li>View all your co-workers along with their basic information.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'How to know the latest happenings or announcements?',
                'description' => '
                    <p>Here’s how you can stay updated:</p>
                    <ul>
                        <li>Go to the "Announcements" section in Employee Self-Service.</li>
                        <li>Read through the latest posts and updates.</li>
                        <li>From the dashboard, you can view the top 10 announcements or news.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'Can I edit my profile?',
                'description' => '
                    <p>Yes, here’s how you can edit your profile:</p>
                    <ul>
                        <li>Go to "My Profile" in Employee Self-Service.</li>
                        <li>Edit your personal information as needed.</li>
                        <li>Save your changes.</li>
                        <li>Wait for HR’s approval to validate the updated information.</li>
                    </ul>
                ',
            ],
            [
                'name' => 'Is there any tutorial or guide I can use as an employee?',
                'description' => '
                    <p>Yes, you can access helpful resources here:</p>
                    <ul>
                        <li>Go to the "Tutorial" section in Employee Self-Service, where you can:</li>
                        <li>Watch YouTube tutorials.</li>
                        <li>Explore FAQs and helpful guides.</li>
                    </ul>
                ',
            ]
            ];
        
        foreach ($data as $data) {
            FAQs::updateOrCreate(
                ['name' => $data['name']], 
                $data
            );
        }

    }
}
