<?php

namespace Database\Seeders;

use App\Models\EmployeeAnnouncements;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'banner' => 'default.jpg', 
                'title' => '📢 Important Update: New Company Holiday Policy for 2025!',
                'content' => "
                    <p>Dear Team,</p>
                    <p>
                        HRIS.com is pleased to announce an update to our company holiday policy, providing more opportunities for employees to enjoy time with their loved ones throughout the year. Starting January 2025, we are expanding the number of paid holidays to include additional days around major holidays and introducing a floating holiday option for cultural or religious observances. This change comes in response to employee feedback, and we are thrilled to offer our team more flexibility and time to recharge.
                    </p>
                    <p>
                        Please log in to the HRIS portal to view the full holiday calendar, including designated company-wide breaks, floating holiday instructions, and guidelines for requesting additional days off. We encourage everyone to take full advantage of these changes to maintain a healthy work-life balance. Questions? Feel free to reach out to HR through the portal or by contacting <a href='mailto:hr@hris.com'>hr@hris.com</a>.
                    </p>
                    <p>
                        Thank you for your dedication and hard work. Enjoy your well-deserved breaks in 2025!
                    </p>
                    <p>Warm regards,<br>The HR Team
                    </p>
                "
            ],
            [
                'banner' => 'default.jpg', 
                'title' => '🚀 New Skill Development Workshops Launching in January 2025',
                'content' => "
                    <p>Hello HRIS Team!</p>
                    <p>
                        As part of our commitment to fostering growth and supporting our employees' professional development, HRIS.com is thrilled to announce the launch of our monthly Skill Development Workshops. Beginning in January, these workshops will cover a variety of topics, from technical skill enhancements like coding and data analysis to soft skills like public speaking, time management, and leadership training.
                    </p>
                    <p>
                        Each month will feature a new theme, led by industry experts and designed to equip you with practical tools and knowledge. These workshops are open to all employees at every level, and each session will be recorded and accessible in the HRIS portal afterward.
                    </p>
                    <p>
                        To kick things off, our January workshop will focus on Effective Communication in the Workplace. Registration is now open—sign up through the HRIS portal under \"Training & Development.\" Don’t miss this opportunity to learn, grow, and connect with colleagues.
                    </p>
                    <p>Let’s make 2025 a year of growth and success!</p>
                    <p>Warm regards,<br>The HR Team</p>
                "
            ],
            [
                'banner' => 'default.jpg', 
                'title' => '📝 Annual Performance Review Period Begins November 15, 2024',
                'content' => "
                    <p>Dear Employees,</p>
                    <p>
                        The annual performance review period at HRIS.com is approaching! Starting November 15, 2024, all employees and managers can log in to the HRIS portal to complete evaluations. This is an essential opportunity for team members to discuss achievements, growth areas, and future goals, setting the stage for a successful year ahead.
                    </p>
                    <p>Here’s what to expect:</p>
                    <ul>
                        <li><strong>Self-Assessments:</strong> Employees are encouraged to complete a self-assessment to reflect on accomplishments and outline areas for development.</li>
                        <li><strong>Manager Feedback:</strong> Managers will provide valuable insights and feedback based on performance throughout the year, including goal setting for 2025.</li>
                        <li><strong>Review Timeline:</strong> All reviews must be submitted by December 10, 2024, to allow HR to compile reports and make recommendations.</li>
                    </ul>
                    <p>
                        As a team dedicated to growth and excellence, we encourage open and constructive conversations during this period. Performance reviews help recognize your contributions and shape our company’s goals. If you have questions or need support, please contact your HR representative.
                    </p>
                    <p>Let’s make this review period productive and positive!</p>
                    <p>Best,<br>The HR Team</p>
                "
            ],
            [
                'banner' => 'default.jpg', 
                'title' => '📢 Open Enrollment for 2025 Benefits: Act Now to Update Your Plans',
                'content' => "
                    <p>Dear HRIS.com Employees,</p>
                    <p>
                        Open Enrollment for 2025 benefits is here! From now until December 1, 2024, you have the opportunity to review, update, or make changes to your health, dental, vision, and retirement plans. This is the only period in which you can make adjustments unless you experience a qualifying life event.
                    </p>
                    <p>Here’s how to participate in Open Enrollment:</p>
                    <ul>
                        <li><strong>Log In:</strong> Access the HRIS portal and navigate to the “Benefits” section.</li>
                        <li><strong>Review Options:</strong> Compare available plans and coverage options to ensure you select the best fit for you and your family.</li>
                        <li><strong>Make Updates:</strong> Confirm or update your selections. Be sure to double-check all information before submitting!</li>
                    </ul>
                    <p>
                        If you have any questions or need assistance, we’re here to help. Our HR benefits team will host two virtual Q&A sessions on November 14 and November 21. You can sign up through the HRIS portal or reach out to <a href='mailto:benefits@hris.com'>benefits@hris.com</a> for individual support.
                    </p>
                    <p>Take charge of your benefits for 2025 and make the most of this enrollment period!</p>
                    <p>Warm regards,<br>The Benefits Team</p>
                "
            ],
            [
                'banner' => 'default.jpg', 
                'title' => '🎉 Exciting News: Updated Employee Recognition Program!',
                'content' => "
                    <p>Hello HRIS Team,</p>
                    <p>
                        HRIS.com is excited to unveil our revamped Employee Recognition Program, designed to honor and celebrate our outstanding employees who consistently go above and beyond. Starting in 2025, our updated program will include new award categories, monthly spotlights, and team-wide rewards for recognized employees.
                    </p>
                    <p>Here’s what’s new:</p>
                    <ul>
                        <li><strong>Monthly Spotlight:</strong> Each month, we will feature a “Star Employee” across our internal channels and in the company newsletter.</li>
                        <li><strong>Award Categories:</strong> From innovation and leadership to collaboration and customer service, employees can be nominated for specific awards that align with their unique contributions.</li>
                        <li><strong>Quarterly Celebrations:</strong> Recognized employees will be invited to quarterly celebrations and rewarded with exciting prizes.</li>
                    </ul>
                    <p>
                        We encourage you to nominate deserving colleagues through the HRIS portal under the “Recognition” section. Let’s come together to celebrate the achievements and hard work of our team. Have questions? Feel free to reach out to the HR team for more details.
                    </p>
                    <p>Let’s make 2025 a year of gratitude and appreciation!</p>
                    <p>Best regards,<br>The HR Team</p>
                "
            ],
        ];
        

        foreach ($announcements as $announcement) {
            EmployeeAnnouncements::updateOrCreate(
                [
                    'title' => $announcement['title'], 
                ], 
                $announcement
            );
        }
    }

}
