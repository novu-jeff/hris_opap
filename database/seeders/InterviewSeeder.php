<?php

namespace Database\Seeders;

use App\Models\Interview;
use App\Models\InterviewItems;
use App\Models\InterviewItemsOptions;
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
                'item' => [
                    [
                        'question' => 'What is HTML?',
                        'response_type' => 'radio',
                        'options' => [
                            'Hypher Text Markup Language',
                            'Hyper Tool Markup Language',
                            'Hyperlink Text Management Language',
                            'Hypertext Machine Language'
                        ]
                    ],
                    [
                        'question' => 'Explain the concept of "closures" in JavaScript.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'What is the difference between GET and POST requests?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'Select the primary languages used in front-end development.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'HTML', 'CSS', 'JavaScript', 'PHP'
                        ]
                    ],
                    [
                        'question' => 'Which of the following are OOP principles?',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Inheritance', 'Encapsulation', 'Abstraction', 'Polymorphism', 'Compilation'
                        ]
                    ],
                    [
                        'question' => 'What is the purpose of version control systems like Git?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Which of the following is a JavaScript framework?',
                        'response_type' => 'radio',
                        'options' => [
                            'React', 'Laravel', 'Django', 'Flask'
                        ]
                    ],
                    [
                        'question' => 'Describe a situation where you used SQL to solve a problem.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Which data structure would be most efficient for implementing a queue?',
                        'response_type' => 'radio',
                        'options' => [
                            'Array', 'Stack', 'Linked List', 'Binary Tree'
                        ]
                    ],
                    [
                        'question' => 'Choose the command(s) used to push code to a Git repository.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'git push', 'git commit', 'git pull', 'git clone'
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Psychological Interview',
                'description' => 'A psychological interview evaluates behavioral, emotional, and interpersonal skills to understand a candidate\'s personality traits and reactions under certain circumstances.',
                'item' => [
                    [
                        'question' => 'How do you usually handle stressful situations at work?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Select the traits that describe you best.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Empathetic', 'Self-motivated', 'Team player', 'Detail-oriented', 'Adaptable'
                        ]
                    ],
                    [
                        'question' => 'Do you prefer working in a team or individually?',
                        'response_type' => 'radio',
                        'options' => [
                            'Team', 'Individually', 'Depends on the situation'
                        ]
                    ],
                    [
                        'question' => 'Describe a time when you had to make a difficult decision and how you handled it.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'What motivates you to perform your best at work?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'When faced with a challenge, what is your typical approach?',
                        'response_type' => 'radio',
                        'options' => [
                            'Analyze and plan first', 'Take immediate action', 'Seek advice from others', 'Take a wait-and-see approach'
                        ]
                    ],
                    [
                        'question' => 'Select the qualities you believe are essential in a leader.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Honesty', 'Vision', 'Decisiveness', 'Empathy', 'Flexibility'
                        ]
                    ],
                    [
                        'question' => 'Describe a time when you had to adapt to a significant change at work.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'How do you prioritize your tasks in a fast-paced environment?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'Which of these best describes your problem-solving approach?',
                        'response_type' => 'radio',
                        'options' => [
                            'Logical and systematic', 'Creative and innovative', 'Practical and hands-on', 'Intuitive and spontaneous'
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Behavioral Interview',
                'description' => 'A behavioral interview evaluates how candidates have handled past situations and challenges, providing insights into their personality, behavior, and problem-solving approach.',
                'item' => [
                    [
                        'question' => 'Describe a time when you had to handle a challenging project with limited resources.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'How do you typically handle feedback from a supervisor?',
                        'response_type' => 'radio',
                        'options' => [
                            'Accept it and improve', 'Feel defensive', 'Ignore it', 'Seek clarification'
                        ]
                    ],
                    [
                        'question' => 'What qualities do you think are important for effective teamwork?',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Communication', 'Reliability', 'Flexibility', 'Trustworthiness', 'Supportiveness'
                        ]
                    ],
                    [
                        'question' => 'Tell us about a time when you had to take the initiative in a project.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'What do you do to stay organized and manage your time effectively?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'Describe a situation where you had to resolve a conflict in the workplace.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Select the characteristics that you believe are essential in a successful employee.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Punctuality', 'Proactivity', 'Attention to detail', 'Curiosity', 'Adaptability'
                        ]
                    ],
                    [
                        'question' => 'Describe a situation where you had to meet a tight deadline. How did you manage it?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'How do you prioritize your tasks when handling multiple projects?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'Which of these approaches best represents your method for handling stress?',
                        'response_type' => 'radio',
                        'options' => [
                            'Focus on tasks', 'Take frequent breaks', 'Seek support', 'Stay calm and plan'
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Management Interview',
                'description' => 'A management interview focuses on leadership qualities, decision-making skills, and the ability to inspire and manage a team.',
                'item' => [
                    [
                        'question' => 'How do you ensure that your team stays motivated?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'Describe a time when you had to make a difficult decision quickly.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Select the qualities you believe are essential in a successful leader.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Integrity', 'Vision', 'Decisiveness', 'Empathy', 'Resilience'
                        ]
                    ],
                    [
                        'question' => 'What is your approach to handling team conflicts?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'How do you delegate tasks to your team?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Which of these best describes your management style?',
                        'response_type' => 'radio',
                        'options' => [
                            'Authoritative', 'Collaborative', 'Hands-off', 'Supportive'
                        ]
                    ],
                    [
                        'question' => 'Describe a time when you had to guide your team through a change.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'How do you measure and track team performance?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'What strategies do you use to develop team members’ skills?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Choose the leadership qualities you think inspire trust in a team.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Transparency', 'Consistency', 'Empathy', 'Confidence', 'Humility'
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Cultural Fit Interview',
                'description' => 'A cultural fit interview assesses a candidate\'s alignment with the company\'s values, work environment, and overall culture.',
                'item' => [
                    [
                        'question' => 'What about our company\'s mission excites you the most?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'Describe a time when your personal values aligned with your workplace.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Select the values you prioritize in a workplace.',
                        'response_type' => 'checkbox',
                        'options' => [
                            'Integrity', 'Innovation', 'Teamwork', 'Work-life balance', 'Customer satisfaction'
                        ]
                    ],
                    [
                        'question' => 'What do you enjoy most about working in a collaborative environment?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'How do you contribute to a positive work culture?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'Which of these qualities best represents your personality?',
                        'response_type' => 'radio',
                        'options' => [
                            'Optimistic', 'Detail-oriented', 'Results-driven', 'Compassionate'
                        ]
                    ],
                    [
                        'question' => 'Describe a time when you had to adapt to a new culture or work environment.',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'What do you think sets our company apart from others in the industry?',
                        'response_type' => 'simple'
                    ],
                    [
                        'question' => 'How do you handle working with diverse personalities and backgrounds?',
                        'response_type' => 'explanatory'
                    ],
                    [
                        'question' => 'What aspect of our company\'s culture do you find most appealing?',
                        'response_type' => 'simple'
                    ]
                ],
            ]
        ];
                

        foreach ($data as $item) {
            $interview = Interview::updateOrCreate(
                ['name' => $item['name']], 
                [
                    'description' => $item['description']
                ]
            );
        
            foreach ($item['item'] as $childItem) {
                $interviewItem = InterviewItems::updateOrCreate(
                    [
                        'interview_id' => $interview->id,
                        'question' => $childItem['question'],
                    ],
                    [
                        'question' => $childItem['question'],
                        'response_type' => $childItem['response_type']
                    ]
                );
        
                // If response type is 'checkbox' or 'radio', add options
                if (in_array($childItem['response_type'], ['checkbox', 'radio']) && isset($childItem['options'])) {
                    foreach ($childItem['options'] as $option) {
                        InterviewItemsOptions::updateOrCreate(
                            [
                                'interview_item_id' => $interviewItem->id,
                                'name' => $option
                            ],
                            [
                                'name' => $option
                            ]
                        );
                    }
                }
            }
        }
        
    }
}
