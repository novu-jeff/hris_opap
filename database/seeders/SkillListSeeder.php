<?php

namespace Database\Seeders;

use App\Models\SkillList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillListSeeder extends Seeder
{
    public function run()
    {
        $skills = [
            // Soft Skills
            ['category' => 'Soft Skills', 'name' => 'Communication'],
            ['category' => 'Soft Skills', 'name' => 'Teamwork'],
            ['category' => 'Soft Skills', 'name' => 'Problem-solving'],
            ['category' => 'Soft Skills', 'name' => 'Adaptability'],
            ['category' => 'Soft Skills', 'name' => 'Time management'],
            ['category' => 'Soft Skills', 'name' => 'Leadership'],
            ['category' => 'Soft Skills', 'name' => 'Conflict resolution'],
            ['category' => 'Soft Skills', 'name' => 'Emotional intelligence'],
            ['category' => 'Soft Skills', 'name' => 'Creativity'],
            ['category' => 'Soft Skills', 'name' => 'Critical thinking'],
            ['category' => 'Soft Skills', 'name' => 'Interpersonal skills'],
            ['category' => 'Soft Skills', 'name' => 'Active listening'],
            ['category' => 'Soft Skills', 'name' => 'Negotiation'],
            ['category' => 'Soft Skills', 'name' => 'Empathy'],
            ['category' => 'Soft Skills', 'name' => 'Flexibility'],
            ['category' => 'Soft Skills', 'name' => 'Decision-making'],
            ['category' => 'Soft Skills', 'name' => 'Networking'],
            ['category' => 'Soft Skills', 'name' => 'Patience'],
            ['category' => 'Soft Skills', 'name' => 'Open-mindedness'],
            ['category' => 'Soft Skills', 'name' => 'Stress management'],

            // Hard Skills
            ['category' => 'Hard Skills', 'name' => 'Data entry'],
            ['category' => 'Hard Skills', 'name' => 'Accounting and finance'],
            ['category' => 'Hard Skills', 'name' => 'Graphic design (Photoshop, Illustrator)'],
            ['category' => 'Hard Skills', 'name' => 'Video editing (Premiere Pro, Final Cut Pro)'],
            ['category' => 'Hard Skills', 'name' => 'Marketing strategies (SEO, PPC, content marketing)'],
            ['category' => 'Hard Skills', 'name' => 'Research methods'],
            ['category' => 'Hard Skills', 'name' => 'Project management (Project Management Software)'],
            ['category' => 'Hard Skills', 'name' => 'Foreign languages (specify languages)'],
            ['category' => 'Hard Skills', 'name' => 'Sales techniques'],
            ['category' => 'Hard Skills', 'name' => 'Legal knowledge (contract law, compliance)'],
            ['category' => 'Hard Skills', 'name' => 'Public speaking and presentation'],
            ['category' => 'Hard Skills', 'name' => 'Event planning and management'],
            ['category' => 'Hard Skills', 'name' => 'Customer service'],
            ['category' => 'Hard Skills', 'name' => 'Technical writing'],
            ['category' => 'Hard Skills', 'name' => 'Data visualization tools (Tableau, Power BI)'],
            ['category' => 'Hard Skills', 'name' => 'Robotics and automation'],
            ['category' => 'Hard Skills', 'name' => 'Supply chain management'],
            ['category' => 'Hard Skills', 'name' => 'Negotiation tactics'],
            ['category' => 'Hard Skills', 'name' => 'Brand management'],
            ['category' => 'Hard Skills', 'name' => 'Social media management'],

            // Technical Skills
            ['category' => 'Technical Skills', 'name' => 'Programming languages (e.g., Python, Java, JavaScript, PHP, Ruby)'],
            ['category' => 'Technical Skills', 'name' => 'Web development (HTML, CSS, JavaScript frameworks)'],
            ['category' => 'Technical Skills', 'name' => 'Database management (SQL, NoSQL, MongoDB, MySQL)'],
            ['category' => 'Technical Skills', 'name' => 'Version control (Git, GitHub, GitLab)'],
            ['category' => 'Technical Skills', 'name' => 'Cloud computing (AWS, Azure, Google Cloud)'],
            ['category' => 'Technical Skills', 'name' => 'Cybersecurity fundamentals'],
            ['category' => 'Technical Skills', 'name' => 'Data analysis (Excel, R, Python libraries)'],
            ['category' => 'Technical Skills', 'name' => 'Machine learning (TensorFlow, scikit-learn)'],
            ['category' => 'Technical Skills', 'name' => 'DevOps practices (CI/CD, Docker, Kubernetes)'],
            ['category' => 'Technical Skills', 'name' => 'Software development methodologies (Agile, Scrum)'],
            ['category' => 'Technical Skills', 'name' => 'Mobile app development (iOS, Android)'],
            ['category' => 'Technical Skills', 'name' => 'UI/UX design principles'],
            ['category' => 'Technical Skills', 'name' => 'API development and integration'],
            ['category' => 'Technical Skills', 'name' => 'Network configuration and management'],
            ['category' => 'Technical Skills', 'name' => 'System administration (Linux, Windows Server)'],
            ['category' => 'Technical Skills', 'name' => 'Game development (Unity, Unreal Engine)'],
            ['category' => 'Technical Skills', 'name' => 'Quality assurance and testing'],
            ['category' => 'Technical Skills', 'name' => 'Technical documentation'],
            ['category' => 'Technical Skills', 'name' => 'Responsive design'],
            ['category' => 'Technical Skills', 'name' => 'SEO basics'],

            // Personal Skills
            ['category' => 'Personal Skills', 'name' => 'Self-motivation'],
            ['category' => 'Personal Skills', 'name' => 'Resilience'],
            ['category' => 'Personal Skills', 'name' => 'Discipline'],
            ['category' => 'Personal Skills', 'name' => 'Initiative'],
            ['category' => 'Personal Skills', 'name' => 'Attention to detail'],
            ['category' => 'Personal Skills', 'name' => 'Goal setting'],
            ['category' => 'Personal Skills', 'name' => 'Self-awareness'],
            ['category' => 'Personal Skills', 'name' => 'Organization'],
            ['category' => 'Personal Skills', 'name' => 'Cultural awareness'],
            ['category' => 'Personal Skills', 'name' => 'Curiosity'],

            // Creative Skills
            ['category' => 'Creative Skills', 'name' => 'Creative writing'],
            ['category' => 'Creative Skills', 'name' => 'Photography'],
            ['category' => 'Creative Skills', 'name' => 'Illustration'],
            ['category' => 'Creative Skills', 'name' => 'Music production'],
            ['category' => 'Creative Skills', 'name' => 'Content creation (blogs, videos)'],
            ['category' => 'Creative Skills', 'name' => 'Copywriting'],
            ['category' => 'Creative Skills', 'name' => 'Fashion design'],
            ['category' => 'Creative Skills', 'name' => 'Animation'],
            ['category' => 'Creative Skills', 'name' => 'Interior design'],
            ['category' => 'Creative Skills', 'name' => 'Visual storytelling'],
        ];

        // Upsert skills (update existing or insert new)
        foreach ($skills as $skill) {
            SkillList::updateOrInsert(
                ['category' => $skill['category'], 'name' => $skill['name']]
            );
        }
    }
}
