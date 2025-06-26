<?php

namespace App\Http\Controllers\Admin\Services;

use App\Helper\Generate;
use App\Http\Controllers\Controller;
use App\Models\EmployeeAccount;
use App\Models\EmployeeChildren;
use App\Models\EmployeeCivilService;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmploymentHistory;
use App\Models\EmployeeInformation;
use App\Models\EmployeeOtherWorks;
use App\Models\EmployeeParents;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSkillsHobbies;
use App\Models\EmployeeTrainings;
use App\Models\EmployementTypes;
use App\Models\Positions;
use App\Models\Sections;

class EmployeeUploadService extends Controller
{

    private function transformDate($value) {
        if(is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        } else {
            return format_date($value, 'carbon_date')->format('Y-m-d');
        }
    }    

    public function uploadEmployeeInformation($data, $schedules) {
        $result = [
            'employee_information' => [
                'inserted' => [
                    'total' => 0,
                    'data' => [],
                ],
                'updated' => [
                    'total' => 0,
                    'data' => [],
                ],
            ],
            'employee_personal' => [
                'inserted' => [
                    'total' => 0,
                    'data' => [],
                ],
                'updated' => [
                    'total' => 0,
                    'data' => [],
                ],
            ],
        ];
    
        foreach ($data as $employeeData) {
    
            // Handle Position
    
            $position = Positions::where('name', 'like', '%' . $employeeData[17] . '%')->first();
            $section = Sections::where('name', 'like', '%' . $employeeData[18] . '%')->first();
            $employment = EmployementTypes::where('name', 'like', '%' . $employeeData[19] . '%')->first();
    
            // Create or Update Employee Information
            $employeeInfo = EmployeeInformation::updateOrCreate(
                ['employee_no' => $employeeData[0]],
                [
                    'shift_id' => $schedules['shift'] ?? null,
                    'schedule_id' => $schedules['schedule'] ?? null, 
                    'bsd_no' => $employeeData[1],
                    'date_hired' => $this->transformDate($employeeData[16]),
                    'section_id' => $section->id ?? null,
                    'position_id' => $position->id ?? null,
                    'employment_type_id' => $employment->id ?? null,
                    'bank_account_no' => $employeeData[15],
                ]
            );
    
            // Check if it was recently created
            if ($employeeInfo->wasRecentlyCreated) {
                $result['employee_information']['inserted']['total']++;
                $result['employee_information']['inserted']['data'][] = [
                    'employee_no' => $employeeData[0],
                    'name' => $employeeData[3] . ' ' . $employeeData[2],
                ];
            } else {
                $result['employee_information']['updated']['total']++;
                $result['employee_information']['updated']['data'][] = [
                    'employee_no' => $employeeData[0],
                    'name' => $employeeData[3] . ' ' . $employeeData[2],
                ];
            }
    
            // Create or Update Employee Personal Data
            $employeePersonal = EmployeePersonal::updateOrCreate(
                ['employee_no' => $employeeData[0]],
                [
                    'bsd_no' => $employeeData[1],
                    'lastname' => $employeeData[2],
                    'firstname' => $employeeData[3],
                    'middlename' => $employeeData[4],
                    'present_address' => $employeeData[5],
                    'sex' => strtolower($employeeData[6]),
                    'civil_status' => strtolower($employeeData[7]),
                    'birthday' => $this->transformDate($employeeData[8]),
                    'age' => is_numeric($employeeData[9]) ? $employeeData[9] : null,
                    'gsis_no' => $employeeData[10],
                    'pagibig_no' => $employeeData[11],
                    'sss_no' => $employeeData[12],
                    'philhealth_no' => $employeeData[13],
                    'tin_no' => $employeeData[14],
                ]
            );
    
            // Check if it was recently created
            if ($employeePersonal->wasRecentlyCreated) {
                $result['employee_personal']['inserted']['total']++;
                $result['employee_personal']['inserted']['data'][] = [
                    'employee_no' => $employeeData[0],
                    'name' => $employeeData[3] . ' ' . $employeeData[2],
                ];
            } else {
                $result['employee_personal']['updated']['total']++;
                $result['employee_personal']['updated']['data'][] = [
                    'employee_no' => $employeeData[0],
                    'name' => $employeeData[3] . ' ' . $employeeData[2],
                ];
            }
    
            // For new accounts
            $this->createAccount($employeeData[0], $employeeData[3], $employeeData[2], $employeeData[21],);
        }
    
        return $result;
    }
    
    
    public function uploadFamilyBackground($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $row) {
            if (empty($row[0])) { 
                continue;
            }
    
            // Update or Create Employee Parents Record
            $record = EmployeeParents::updateOrCreate(
                [
                    'employee_no' => $row[0]
                ],
                [
                    'spouse_surname' => $row[1],
                    'spouse_firstname' => $row[2],
                    'spouse_middlename' => $row[3],
                    'spouse_suffix' => $row[4],
                    'spouse_occupation' => $row[5],
                    'spouse_business_name_employer' => $row[6],
                    'spouse_business_address' => $row[7],
                    'spouse_contact_no' => $row[8],
                    'father_surname' => $row[9],
                    'father_firstname' => $row[10],
                    'father_middlename' => $row[11],
                    'father_suffix' => $row[12],
                    'mother_surname' => $row[13],
                    'mother_firstname' => $row[14],
                    'mother_middlename' => $row[15],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($record->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $row[0],
                    'spouse_name' => $row[2] . ' ' . $row[1], // Assuming firstname and surname
                    'father_name' => $row[10] . ' ' . $row[9], // Assuming firstname and surname
                    'mother_name' => $row[14] . ' ' . $row[13], // Assuming firstname and surname
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $row[0],
                    'spouse_name' => $row[2] . ' ' . $row[1], // Assuming firstname and surname
                    'father_name' => $row[10] . ' ' . $row[9], // Assuming firstname and surname
                    'mother_name' => $row[14] . ' ' . $row[13], // Assuming firstname and surname
                ];
            }
        }
    
        // Return the result
        return $result;
    }    
    
    public function uploadChildren($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $childData) {
            if (empty($childData[0])) { 
                continue;
            }
    
            // Update or Create Employee Children Record
            $childRecord = EmployeeChildren::updateOrCreate(
                [
                    'employee_no' => $childData[0],
                    'firstname' => $childData[1],
                    'middlename' => $childData[2],
                    'lastname' => $childData[3],
                ],
                [
                    'employee_no' => $childData[0],
                    'firstname' => $childData[1],
                    'middlename' => $childData[2],
                    'lastname' => $childData[3],
                    'birthdate' => $childData[4],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($childRecord->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $childData[0],
                    'child_name' => $childData[1] . ' ' . $childData[2] . ' ' . $childData[3], // Full name
                    'birthdate' => $childData[4],
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $childData[0],
                    'child_name' => $childData[1] . ' ' . $childData[2] . ' ' . $childData[3], // Full name
                    'birthdate' => $childData[4],
                ];
            }
        }
    
        // Return the result
        return $result;
    }    
    
    public function uploadEducation($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $educationData) {
            if (empty($educationData[0])) { 
                continue;
            }
    
            // Perform update or create operation
            $education = EmployeeEducation::updateOrCreate(
                [
                    'employee_no' => $educationData[0],
                    'level' => $educationData[1],
                    'school_name' => $educationData[2],
                    'course' => $educationData[3],
                    'from_year' => $educationData[4],
                    'to_year' => $educationData[5],
                ],
                [
                    'employee_no' => $educationData[0],
                    'level' => $educationData[1],
                    'school_name' => $educationData[2],
                    'course' => $educationData[3],
                    'from_year' => $educationData[4],
                    'to_year' => $educationData[5],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($education->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $educationData[0],
                    'level' => $educationData[1],
                    'school_name' => $educationData[2],
                    'course' => $educationData[3],
                    'from_year' => $educationData[4],
                    'to_year' => $educationData[5],
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $educationData[0],
                    'level' => $educationData[1],
                    'school_name' => $educationData[2],
                    'course' => $educationData[3],
                    'from_year' => $educationData[4],
                    'to_year' => $educationData[5],
                ];
            }
        }
    
        // Return the result
        return $result;
    }    
    
    public function uploadEmploymentHistory($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $historyData) {
            if (empty($historyData[0])) { 
                continue;
            }
    
            // Perform update or create operation
            $employmentHistory = EmployeeEmploymentHistory::updateOrCreate(
                [
                    'employee_no' => $historyData[0],
                    'department' => $historyData[2],
                    'company_name' => $historyData[3],
                ], 
                [
                    'employee_no' => $historyData[0],
                    'position' => $historyData[1],
                    'department' => $historyData[2],
                    'company_name' => $historyData[3],
                    'monthly_salary' => $historyData[4],
                    'employment_status' => $historyData[5],
                    'isGovernment' => $historyData[6],
                    'from_year' => $historyData[7],
                    'to_year' => $historyData[8],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($employmentHistory->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $historyData[0],
                    'position' => $historyData[1],
                    'department' => $historyData[2],
                    'company_name' => $historyData[3],
                    'monthly_salary' => $historyData[4],
                    'employment_status' => $historyData[5],
                    'isGovernment' => $historyData[6],
                    'from_year' => $historyData[7],
                    'to_year' => $historyData[8],
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $historyData[0],
                    'position' => $historyData[1],
                    'department' => $historyData[2],
                    'company_name' => $historyData[3],
                    'monthly_salary' => $historyData[4],
                    'employment_status' => $historyData[5],
                    'isGovernment' => $historyData[6],
                    'from_year' => $historyData[7],
                    'to_year' => $historyData[8],
                ];
            }
        }
    
        // Return the result
        return $result;
    }    
    
    public function uploadCivilService($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $csData) {
            if (empty($csData[0])) { 
                continue;
            }
    
            // Perform update or create operation
            $civilService = EmployeeCivilService::updateOrCreate(
                [
                    'employee_no' => $csData[0],
                    'certification' => $csData[1],
                    'date_exam' => $this->transformDate($csData[3]),
                ], 
                [
                    'employee_no' => $csData[0],
                    'certification' => $csData[1],
                    'rating' => $csData[2],
                    'date_exam' => $this->transformDate($csData[3]),
                    'place_exam' => $csData[4],
                    'license_no' => $csData[5],
                    'date_validity' => $this->transformDate($csData[6]),
                ]
            );
    
            // Check if the record was recently created or updated
            if ($civilService->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $csData[0],
                    'certification' => $csData[1],
                    'rating' => $csData[2],
                    'date_exam' => $this->transformDate($csData[3]),
                    'place_exam' => $csData[4],
                    'license_no' => $csData[5],
                    'date_validity' => $this->transformDate($csData[6]),
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $csData[0],
                    'certification' => $csData[1],
                    'rating' => $csData[2],
                    'date_exam' => $this->transformDate($csData[3]),
                    'place_exam' => $csData[4],
                    'license_no' => $csData[5],
                    'date_validity' => $this->transformDate($csData[6]),
                ];
            }
        }
    
        // Return the result
        return $result;
    }    
    
    public function uploadTrainings($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $trainingData) {
            if (empty($trainingData[0])) { 
                continue;
            }
    
            // Perform update or create operation
            $training = EmployeeTrainings::updateOrCreate(
                [
                    'employee_no' => $trainingData[0],
                    'type' => $trainingData[1],
                    'name' => $trainingData[2],
                    'date_from' => $this->transformDate($trainingData[3]),
                    'date_to' => $this->transformDate($trainingData[4]),
                ], 
                [
                    'employee_no' => $trainingData[0],
                    'type' => $trainingData[1],
                    'name' => $trainingData[2],
                    'date_from' => $this->transformDate($trainingData[3]),
                    'date_to' => $this->transformDate($trainingData[4]),
                    'consumed_hours' => $trainingData[5],
                    'sponsored_by' => $trainingData[6],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($training->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $trainingData[0],
                    'type' => $trainingData[1],
                    'name' => $trainingData[2],
                    'date_from' => $this->transformDate($trainingData[3]),
                    'date_to' => $this->transformDate($trainingData[4]),
                    'consumed_hours' => $trainingData[5],
                    'sponsored_by' => $trainingData[6],
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $trainingData[0],
                    'type' => $trainingData[1],
                    'name' => $trainingData[2],
                    'date_from' => $this->transformDate($trainingData[3]),
                    'date_to' => $this->transformDate($trainingData[4]),
                    'consumed_hours' => $trainingData[5],
                    'sponsored_by' => $trainingData[6],
                ];
            }
        }
    
        // Return the result
        return $result;
    }    
    
    public function uploadOtherWorks($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $workData) {
            if (empty($workData[0])) { 
                continue;
            }
    
            // Perform update or create operation
            $work = EmployeeOtherWorks::updateOrCreate(
                [
                    'employee_no' => $workData[0],
                    'organization' => $workData[1],
                    'address' => $workData[2],
                    'date_from' => $this->transformDate($workData[3]),
                    'date_to' => $this->transformDate($workData[4]),
                ], 
                [
                    'employee_no' => $workData[0],
                    'organization' => $workData[1],
                    'address' => $workData[2],
                    'date_from' => $this->transformDate($workData[3]),
                    'date_to' => $this->transformDate($workData[4]),
                    'consumed_hours' => $workData[5],
                    'position' => $workData[6],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($work->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $workData[0],
                    'organization' => $workData[1],
                    'address' => $workData[2],
                    'date_from' => $this->transformDate($workData[3]),
                    'date_to' => $this->transformDate($workData[4]),
                    'consumed_hours' => $workData[5],
                    'position' => $workData[6],
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $workData[0],
                    'organization' => $workData[1],
                    'address' => $workData[2],
                    'date_from' => $this->transformDate($workData[3]),
                    'date_to' => $this->transformDate($workData[4]),
                    'consumed_hours' => $workData[5],
                    'position' => $workData[6],
                ];
            }
        }
    
        // Return the result
        return $result;
    }
    
    public function uploadSkills($data) {
        // Initialize result counters
        $result = [
            'inserted' => [
                'total' => 0,
                'data' => [],
            ],
            'updated' => [
                'total' => 0,
                'data' => [],
            ],
        ];
    
        foreach ($data as $skillData) {
            if (empty($skillData[0])) { 
                continue;
            }
    
            // Perform update or create operation
            $skill = EmployeeSkillsHobbies::updateOrCreate(
                [
                    'employee_no' => $skillData[0],
                    'name' => $skillData[1],
                    'organization' => $skillData[3],
                ], 
                [
                    'employee_no' => $skillData[0],
                    'name' => $skillData[1],
                    'recognition' => $skillData[2],
                    'organization' => $skillData[3],
                ]
            );
    
            // Check if the record was recently created or updated
            if ($skill->wasRecentlyCreated) {
                $result['inserted']['total']++;
                $result['inserted']['data'][] = [
                    'employee_no' => $skillData[0],
                    'name' => $skillData[1],
                    'recognition' => $skillData[2],
                    'organization' => $skillData[3],
                ];
            } else {
                $result['updated']['total']++;
                $result['updated']['data'][] = [
                    'employee_no' => $skillData[0],
                    'name' => $skillData[1],
                    'recognition' => $skillData[2],
                    'organization' => $skillData[3],
                ];
            }
        }
    
        // Return the result
        return $result;
    }  
    
    private function createAccount($employeeNo, $firstName, $lastName, $email) {

        set_time_limit(0);
        
        $generate = new Generate;
        $email_id = $generate->email($employeeNo, $firstName, $lastName);

        $hash = password_hash('password', PASSWORD_BCRYPT, [
            'cost' => 10,
        ]);

        $user = EmployeeAccount::updateOrCreate(
            ['employee_no' => $employeeNo],
            [
                'email' => $email,
                'email_id' => $email_id,
                'password' => $hash
            ]
        );

        $user->assignRole('employee');
    }


}