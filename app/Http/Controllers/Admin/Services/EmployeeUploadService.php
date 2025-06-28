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
use App\Models\Tranche;

class EmployeeUploadService extends Controller
{

    public function transformDate($value) {
        if(is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        } else {
            return format_date($value, 'carbon_date')->format('Y-m-d');
        }
    }    

    private function getMonthlySalary($eligible, $position_id, $step_id)
    {
        $salaryGrade = Positions::where('id', $position_id)->value('salary_grade');

        if (empty($salaryGrade) || empty($step_id)) {
            return 0;
        }

        $stepColumn = 'step_' . $step_id;

        $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn) {
            $query->where('salary_grade', $salaryGrade)->select('id', 'tranche_id', 'salary_grade', $stepColumn);
        }])->where('eligible', $eligible)->first();

        if ($activeTranche && $activeTranche->items->isNotEmpty()) {
            return floatval($activeTranche->items->first()->$stepColumn ?? 0);
        }

        return 0;
    }
    
    public function uploadEmployeeInformation($data, $schedules)
    {
        $product = strtolower(env('APP_PRODUCT'));

        foreach ($data as $employeeData) {
            if ($product === 'government') {
                $indexes = [
                    'job_category' => 17,
                    'position' => 18,
                    'section' => 19,
                    'company_name' => null,
                    'date_hired' => 16,
                    'bank_account_no' => 15,
                    'gsis' => 11,
                    'pagibig' => 12,
                    'philhealth' => 13,
                    'tin' => 14,
                    'email' => 20,
                    'monthly_rate' => null, 
                ];
            } else {
                $indexes = [
                    'job_category' => 18,
                    'position' => 19,
                    'section' => 20,
                    'company_name' => 16,
                    'date_hired' => 17,
                    'bank_account_no' => 15,
                    'gsis' => null,
                    'pagibig' => 11,
                    'philhealth' => 13,
                    'tin' => 14,
                    'email' => 21,
                    'sss' => 12,
                    'monthly_rate' => 21,
                ];
            }

            $jobCategoryName = trim($employeeData[$indexes['job_category']] ?? '');
            $positionName = trim($employeeData[$indexes['position']] ?? '');
            $sectionName = trim($employeeData[$indexes['section']] ?? '');

            $jobCategory = !empty($jobCategoryName)
                ? EmployementTypes::firstOrCreate(['name' => $jobCategoryName])
                : null;

            $position = !empty($positionName)
                ? Positions::firstOrCreate(['name' => $positionName])
                : null;

            $section = !empty($sectionName)
                ? Sections::firstOrCreate(['name' => $sectionName])
                : null;

            $monthlyRate = 0;
            if ($product === 'government') {
                $monthlyRate = $this->getMonthlySalary(
                    $jobCategory?->id ?? '',
                    $position?->id ?? '',
                    1 
                );
            } else {
                $monthlyRate = floatval($employeeData[$indexes['monthly_rate']] ?? 0);
            }

            $employeeInfo = EmployeeInformation::updateOrCreate(
                ['employee_no' => $employeeData[0]],
                [
                    'bsd_no' => $employeeData[1],
                    'shift_id' => $schedules['shift'] ?? null,
                    'schedule_id' => $schedules['schedule'] ?? null,
                    'section_id' => $section?->id,
                    'date_hired' => $this->transformDate($employeeData[$indexes['date_hired']] ?? null),
                    'position_id' => $position?->id,
                    'employment_type_id' => $jobCategory?->id,
                    'bank_account_no' => $employeeData[$indexes['bank_account_no']] ?? null,
                    'monthly_rate' => $monthlyRate,
                ]
            );

            $personalData = [
                'bsd_no' => $employeeData[1],
                'lastname' => $employeeData[2],
                'firstname' => $employeeData[3],
                'middlename' => $employeeData[4],
                'present_address' => $employeeData[5],
                'sex' => strtolower($employeeData[7] ?? ''),
                'civil_status' => strtolower($employeeData[8] ?? ''),
                'birthday' => $this->transformDate($employeeData[9] ?? null),
                'age' => $employeeData[10] ?? null,
                'pagibig_no' => $employeeData[$indexes['pagibig']] ?? null,
                'philhealth_no' => $employeeData[$indexes['philhealth']] ?? null,
                'tin_no' => $employeeData[$indexes['tin']] ?? null,
            ];

            if ($product === 'government') {
                $personalData['gsis_no'] = $employeeData[$indexes['gsis']] ?? null;
            } else {
                $personalData['sss_no'] = $employeeData[$indexes['sss']] ?? null;
            }

            EmployeePersonal::updateOrCreate(
                ['employee_no' => $employeeData[0]],
                $personalData
            );

            $this->createAccount(
                $employeeData[0],
                $employeeData[3],
                $employeeData[2],
                $employeeData[$indexes['email']] ?? null
            );
        }
    }


    public function uploadFamilyBackground($data) {
    
        foreach ($data as $row) {
            if (empty($row[0])) { 
                continue;
            }
    
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

        }
    
        return;
    }    
    
    public function uploadChildren($data) {
    
        foreach ($data as $childData) {
            if (empty($childData[0])) { 
                continue;
            }
    
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
    
        }
    
        return;
    }    
    
    public function uploadEducation($data) {

    
        foreach ($data as $educationData) {
            if (empty($educationData[0])) { 
                continue;
            }
    
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
    
        }
    
        return;
    }    
    
    public function uploadEmploymentHistory($data) {

    
        foreach ($data as $historyData) {
            if (empty($historyData[0])) { 
                continue;
            }
    
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
        }
    
        return;
    }    
    
    public function uploadCivilService($data) {
    
        foreach ($data as $csData) {
            if (empty($csData[0])) { 
                continue;
            }
    
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
    
        }
    
        return;
    }    
    
    public function uploadTrainings($data) {
    
        foreach ($data as $trainingData) {
            if (empty($trainingData[0])) { 
                continue;
            }
    
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
    
        }
    
        return;
    }    
    
    public function uploadOtherWorks($data) {
    
        foreach ($data as $workData) {
            if (empty($workData[0])) { 
                continue;
            }
    
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

        }
    
        return;
    }
    
    public function uploadSkills($data) {

        foreach ($data as $skillData) {
            if (empty($skillData[0])) { 
                continue;
            }
    
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
        }
    
        return;
    }  
    
    private function createAccount($employeeNo, $firstName, $lastName, $email) {
        
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