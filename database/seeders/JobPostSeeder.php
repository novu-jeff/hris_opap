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
                'setup' => 'work from home',
                'type' => 'regular',
                'min_salary' => 60000,
                'max_salary' => 80000,
                'description' => '<p>As a <strong>Software Engineer</strong> at <em>Tech Innovators Inc.</em>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Develop and maintain web applications using modern technologies.</li>
                                              <li>Collaborate with cross-functional teams to define and implement solutions.</li>
                                              <li>Participate in code reviews to ensure high-quality code standards.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Bachelor\'s degree in Computer Science or related field.</li>
                                              <li>Experience with JavaScript, HTML, CSS, and backend frameworks.</li>
                                              <li>Strong problem-solving skills and attention to detail.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Frontend Developer',
                'company_name' => 'Creative Minds Co.',
                'location' => 'San Francisco, USA',
                'setup' => 'hybrid',
                'type' => 'part time',
                'min_salary' => 40000,
                'max_salary' => 55000,
                'description' => '<p>Join <strong>Creative Minds Co.</strong> as a <strong>Frontend Developer</strong> where you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Work on designing and implementing UI/UX for web applications.</li>
                                              <li>Utilize modern JavaScript frameworks and libraries.</li>
                                              <li>Ensure responsiveness and performance of applications.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience as a Frontend Developer.</li>
                                              <li>Proficiency in JavaScript, CSS, and HTML.</li>
                                              <li>Experience with responsive design techniques.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Data Scientist',
                'company_name' => 'DataWorks Ltd.',
                'location' => 'Toronto, Canada',
                'setup' => 'onsite',
                'type' => 'contractual',
                'min_salary' => 70000,
                'max_salary' => 90000,
                'description' => '<p>At <strong>DataWorks Ltd.</strong>, the <strong>Data Scientist</strong> will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Analyze and interpret complex data to help drive decision-making.</li>
                                              <li>Develop predictive models and machine learning algorithms.</li>
                                              <li>Present findings to stakeholders in an understandable manner.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Master\'s degree in Data Science, Statistics, or a related field.</li>
                                              <li>Strong programming skills in Python or R.</li>
                                              <li>Experience with data visualization tools.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Graphic Designer',
                'company_name' => 'Creative Agency',
                'location' => 'Remote',
                'setup' => 'work from home',
                'type' => 'freelance',
                'min_salary' => 35000,
                'max_salary' => 50000,
                'description' => '<p>As a <strong>Graphic Designer</strong> for <strong>Creative Agency</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Create visual concepts and design layouts for various projects.</li>
                                              <li>Collaborate with clients to understand their vision and requirements.</li>
                                              <li>Use design software to produce high-quality graphics.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience as a Graphic Designer.</li>
                                              <li>Strong portfolio showcasing design skills.</li>
                                              <li>Proficiency in Adobe Creative Suite (Photoshop, Illustrator, etc.).</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Project Manager',
                'company_name' => 'Business Solutions Inc.',
                'location' => 'Los Angeles, USA',
                'setup' => 'onsite',
                'type' => 'regular',
                'min_salary' => 70000,
                'max_salary' => 90000,
                'description' => '<p>As a <strong>Project Manager</strong> at <strong>Business Solutions Inc.</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Oversee project progress and coordinate team activities.</li>
                                              <li>Communicate effectively with clients and stakeholders.</li>
                                              <li>Manage budgets and timelines to ensure successful project delivery.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Bachelor\'s degree in Business Administration or related field.</li>
                                              <li>Proven experience in project management.</li>
                                              <li>Strong leadership and communication skills.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Web Developer',
                'company_name' => 'Tech Agency',
                'location' => 'Hybrid',
                'setup' => 'hybrid',
                'type' => 'regular',
                'min_salary' => 50000,
                'max_salary' => 75000,
                'description' => '<p>Join <strong>Tech Agency</strong> as a <strong>Web Developer</strong> where you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Build and maintain websites using modern web technologies.</li>
                                              <li>Work closely with designers to implement visual elements.</li>
                                              <li>Debug and troubleshoot issues as they arise.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Experience with HTML, CSS, JavaScript, and PHP.</li>
                                              <li>Familiarity with database management systems.</li>
                                              <li>Strong attention to detail and problem-solving skills.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'SEO Specialist',
                'company_name' => 'Marketing Pros',
                'location' => 'Remote',
                'setup' => 'work from home',
                'type' => 'freelance',
                'min_salary' => 40000,
                'max_salary' => 60000,
                'description' => '<p>As an <strong>SEO Specialist</strong> at <strong>Marketing Pros</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Optimize website content for search engines and improve visibility.</li>
                                              <li>Conduct keyword research and implement strategies.</li>
                                              <li>Analyze performance metrics and provide recommendations.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience in SEO and digital marketing.</li>
                                              <li>Strong analytical skills and attention to detail.</li>
                                              <li>Familiarity with SEO tools (e.g., Google Analytics, SEMrush).</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Content Writer',
                'company_name' => 'Writing Co.',
                'location' => 'Remote',
                'setup' => 'work from home',
                'type' => 'freelance',
                'min_salary' => 30000,
                'max_salary' => 50000,
                'description' => '<p>Join <strong>Writing Co.</strong> as a <strong>Content Writer</strong> where you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Write and edit content for various online platforms.</li>
                                              <li>Research topics to ensure accurate and engaging writing.</li>
                                              <li>Collaborate with editors and other writers.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience as a content writer or similar role.</li>
                                              <li>Strong writing and editing skills.</li>
                                              <li>Ability to meet deadlines and manage multiple projects.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'UX/UI Designer',
                'company_name' => 'Design Studio',
                'location' => 'New York, USA',
                'setup' => 'onsite',
                'type' => 'regular',
                'min_salary' => 60000,
                'max_salary' => 80000,
                'description' => '<p>As a <strong>UX/UI Designer</strong> at <strong>Design Studio</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Design user interfaces for web and mobile applications.</li>
                                              <li>Conduct user research and usability testing.</li>
                                              <li>Collaborate with development teams to implement designs.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience as a UX/UI designer.</li>
                                              <li>Strong portfolio showcasing design projects.</li>
                                              <li>Familiarity with design tools (e.g., Sketch, Adobe XD).</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Network Administrator',
                'company_name' => 'IT Solutions',
                'location' => 'Seattle, USA',
                'setup' => 'onsite',
                'type' => 'regular',
                'min_salary' => 50000,
                'max_salary' => 70000,
                'description' => '<p>Join <strong>IT Solutions</strong> as a <strong>Network Administrator</strong>:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Manage and monitor network infrastructure.</li>
                                              <li>Troubleshoot and resolve network issues.</li>
                                              <li>Implement security protocols to safeguard the network.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience as a Network Administrator.</li>
                                              <li>Strong knowledge of networking protocols and security.</li>
                                              <li>Certifications (e.g., CCNA) are a plus.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Database Administrator',
                'company_name' => 'DataCorp',
                'location' => 'Remote',
                'setup' => 'work from home',
                'type' => 'contractual',
                'min_salary' => 60000,
                'max_salary' => 80000,
                'description' => '<p>At <strong>DataCorp</strong>, the <strong>Database Administrator</strong> will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Design and maintain database systems.</li>
                                              <li>Monitor database performance and troubleshoot issues.</li>
                                              <li>Implement backup and recovery procedures.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Bachelor\'s degree in Computer Science or related field.</li>
                                              <li>Proven experience with SQL and database management.</li>
                                              <li>Strong analytical and problem-solving skills.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Cloud Engineer',
                'company_name' => 'Cloud Services Inc.',
                'location' => 'Austin, USA',
                'setup' => 'hybrid',
                'type' => 'regular',
                'min_salary' => 80000,
                'max_salary' => 100000,
                'description' => '<p>As a <strong>Cloud Engineer</strong> at <strong>Cloud Services Inc.</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Design and implement cloud infrastructure solutions.</li>
                                              <li>Manage cloud resources and optimize costs.</li>
                                              <li>Ensure security and compliance in cloud environments.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Bachelor\'s degree in Computer Science or related field.</li>
                                              <li>Experience with cloud platforms (AWS, Azure, GCP).</li>
                                              <li>Strong knowledge of networking and security principles.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'DevOps Engineer',
                'company_name' => 'DevOps Innovations',
                'location' => 'Remote',
                'setup' => 'work from home',
                'type' => 'regular',
                'min_salary' => 70000,
                'max_salary' => 90000,
                'description' => '<p>At <strong>DevOps Innovations</strong>, the <strong>DevOps Engineer</strong> will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Automate and streamline operations and processes.</li>
                                              <li>Monitor system performance and troubleshoot issues.</li>
                                              <li>Collaborate with development teams to improve deployment processes.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Experience with CI/CD tools and practices.</li>
                                              <li>Strong knowledge of scripting languages.</li>
                                              <li>Familiarity with containerization (Docker, Kubernetes).</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Cybersecurity Analyst',
                'company_name' => 'SecureNet',
                'location' => 'Boston, USA',
                'setup' => 'onsite',
                'type' => 'regular',
                'min_salary' => 80000,
                'max_salary' => 100000,
                'description' => '<p>As a <strong>Cybersecurity Analyst</strong> at <strong>SecureNet</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Monitor security systems and respond to incidents.</li>
                                              <li>Conduct security assessments and audits.</li>
                                              <li>Implement security measures to protect sensitive data.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Bachelor\'s degree in Cybersecurity or related field.</li>
                                              <li>Experience with security tools and frameworks.</li>
                                              <li>Certifications (CISSP, CEH) are a plus.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Mobile App Developer',
                'company_name' => 'App Solutions',
                'location' => 'Seattle, USA',
                'setup' => 'hybrid',
                'type' => 'regular',
                'min_salary' => 70000,
                'max_salary' => 90000,
                'description' => '<p>Join <strong>App Solutions</strong> as a <strong>Mobile App Developer</strong>:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Design and develop mobile applications for iOS and Android.</li>
                                              <li>Collaborate with product managers and designers.</li>
                                              <li>Ensure app performance and responsiveness.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Experience with Swift, Kotlin, or React Native.</li>
                                              <li>Strong knowledge of mobile app development principles.</li>
                                              <li>Portfolio of mobile apps is a plus.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Artificial Intelligence Engineer',
                'company_name' => 'AI Solutions',
                'location' => 'Remote',
                'setup' => 'work from home',
                'type' => 'regular',
                'min_salary' => 90000,
                'max_salary' => 110000,
                'description' => '<p>As an <strong>Artificial Intelligence Engineer</strong> at <strong>AI Solutions</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Develop AI models and algorithms for various applications.</li>
                                              <li>Collaborate with data scientists to implement solutions.</li>
                                              <li>Optimize and maintain AI systems.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Master\'s degree in AI, Machine Learning, or related field.</li>
                                              <li>Strong programming skills in Python and ML frameworks.</li>
                                              <li>Experience with cloud AI services is a plus.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Frontend Developer',
                'company_name' => 'Web Solutions',
                'location' => 'Chicago, USA',
                'setup' => 'onsite',
                'type' => 'regular',
                'min_salary' => 60000,
                'max_salary' => 80000,
                'description' => '<p>As a <strong>Frontend Developer</strong> at <strong>Web Solutions</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Develop user-friendly web interfaces.</li>
                                              <li>Collaborate with designers to create engaging experiences.</li>
                                              <li>Optimize applications for maximum speed and scalability.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proficiency in HTML, CSS, and JavaScript.</li>
                                              <li>Experience with frontend frameworks (React, Vue, Angular).</li>
                                              <li>Strong problem-solving skills.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Backend Developer',
                'company_name' => 'Code Factory',
                'location' => 'Los Angeles, USA',
                'setup' => 'work from home',
                'type' => 'contractual',
                'min_salary' => 70000,
                'max_salary' => 90000,
                'description' => '<p>Join <strong>Code Factory</strong> as a <strong>Backend Developer</strong>:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Develop and maintain server-side applications.</li>
                                              <li>Integrate user-facing elements with server-side logic.</li>
                                              <li>Implement security and data protection measures.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proficiency in server-side languages (Node.js, PHP, Python).</li>
                                              <li>Experience with databases (SQL, NoSQL).</li>
                                              <li>Strong understanding of RESTful APIs.</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
            ],
            [
                'position' => 'Game Developer',
                'company_name' => 'Gaming Studios',
                'location' => 'San Francisco, USA',
                'setup' => 'onsite',
                'type' => 'regular',
                'min_salary' => 80000,
                'max_salary' => 100000,
                'description' => '<p>As a <strong>Game Developer</strong> at <strong>Gaming Studios</strong>, you will:</p>
                                  <ul>
                                      <li><strong>Responsibilities:</strong>
                                          <ul>
                                              <li>Design and develop interactive games.</li>
                                              <li>Collaborate with artists and designers.</li>
                                              <li>Optimize game performance and user experience.</li>
                                          </ul>
                                      </li>
                                      <li><strong>Qualifications:</strong>
                                          <ul>
                                              <li>Proven experience in game development.</li>
                                              <li>Strong programming skills in C# or C++.</li>
                                              <li>Familiarity with game engines (Unity, Unreal Engine).</li>
                                          </ul>
                                      </li>
                                  </ul>',
                'slots' => 100,
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
