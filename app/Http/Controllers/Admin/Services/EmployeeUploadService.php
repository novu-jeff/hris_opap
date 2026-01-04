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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmployeeUploadService extends Controller
{

    private function normalizeHeader($header)
    {
        return strtolower(trim($header));
    }


    public function transformDate($value)
{
    if (empty($value)) {
        return null;
    }

    if (is_numeric($value)) {
        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
            ->format('Y-m-d');
    }

    try {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
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
    


public function uploadEmployeeInformation(array $rows, array $schedules)
{
    if (count($rows) === 0) {
        Log::warning('Employee Information sheet is emptys');
        return;
    }

    $expectedHeaders = [
        'employee no.',
        'bsd no.',
        'lastname',
        'firstname',
        'middlename',
        'address',
        'sex',
        'civil status',
        'birthday',
        'age',
        'gsis id',
        'pagibig id',
        'sss id',
        'philhealth id',
        'tin id',
        'bank account no.',
        'date hired',
        'position',
        'monthly salary',
        'job category',
        'email',
        'unit'
    ];

    // Normalize first row
    $firstRow = array_map(fn($v) => strtolower(trim((string) $v)), $rows[0]);

    // Detect header row
    $headerMatches = array_intersect($expectedHeaders, $firstRow);
    $hasHeaderRow = count($headerMatches) >= 3;

    Log::info('Employee Information header detection', [
        'has_header' => $hasHeaderRow,
        'matches' => array_values($headerMatches),
    ]);

    // Build column map
    $columnMap = [];
    if ($hasHeaderRow) {
        foreach ($expectedHeaders as $header) {
            $columnMap[$header] = array_search($header, $firstRow);
        }
        $startRow = 1;
    } else {
        $columnMap = array_combine($expectedHeaders, range(0, count($expectedHeaders) - 1));
        $startRow = 0;
    }

    // Process each row
    for ($i = $startRow; $i < count($rows); $i++) {
        $row = $rows[$i];

        // Map row data
        $data = [];
        foreach ($columnMap as $field => $index) {
            $data[$field] = $row[$index] ?? null;
        }

        if (empty($data['employee no.'])) {
            Log::warning('Skipping row without employee number', ['row' => $i + 1]);
            continue;
        }

        // Normalize dates and numeric fields
        $data['birthday'] = $this->transformDate($data['birthday']);
        $data['date hired'] = $this->transformDate($data['date hired']);
        $data['monthly salary'] = is_numeric($data['monthly salary'])
            ? (float) $data['monthly salary']
            : null;

         $jobCategoryName = ucfirst(strtolower(trim($data['job category'] ?? '')));
            $positionName = trim($data['position'] ?? '');
            $sectionName = trim($data['section'] ?? '');

            $jobCategory = !empty($jobCategoryName)
                ? EmployementTypes::firstOrCreate(['name' => $jobCategoryName])
                : null;
            Log::info("Job Category processed", ['name' => $jobCategoryName, 'id' => $jobCategory?->id]);

            $position = !empty($positionName)
                ? Positions::firstOrCreate(['name' => $positionName])
                : null;
            Log::info("Position processed", ['name' => $positionName, 'id' => $position?->id]);

         /*   $section = !empty($sectionName)
                ? Sections::firstOrCreate(['name' => $sectionName])
                : null;
            Log::info("Section processed", ['name' => $sectionName, 'id' => $section?->id]);   */ 

        Log::info('Uploading employee', [
            'row' => $i + 1,
            'employee_no' => $data['employee no.']
        ]);

        // =========================
        // EmployeeInformation (HR/payroll)
        // =========================
        EmployeeInformation::updateOrCreate(
            ['employee_no' => $data['employee no.']],
            [
                'bsd_no'          => $data['bsd no.'],
                'bank_account_no' => $data['bank account no.'],
                'date_hired' => $this->transformDate($data['date hired'] ?? null),
                'position_id'        => $position?->id,
                'salary'  => $data['monthly salary'],
                'employment_type_id'    => $jobCategory?->id,
                'email'           => $data['email'],
                'unit'            => $data['unit'],
                'shift_id'        => $schedules['shift'] ?? null,
                'schedule_id'     => $schedules['schedule'] ?? null,
            ]
        );

        // =========================
        // EmployeePersonal (personal info)
        // ==========================
        EmployeePersonal::updateOrCreate(
            ['employee_no' => $data['employee no.']],
            [
                'lastname'      => $data['lastname'],
                'firstname'     => $data['firstname'],
                'middlename'    => $data['middlename'],
                'present_address'=> $data['address'],
                'sex'           => strtolower($data['sex'] ?? ''),
                'civil_status'  => strtolower($data['civil status'] ?? ''),
                'birthday'      => $data['birthday'],
                'age'           => $data['age'],
                'gsis_no'       => $data['gsis id'],
                'pagibig_no'    => $data['pagibig id'],
                'sss_no'        => $data['sss id'],
                'philhealth_no' => $data['philhealth id'],
                'tin_no'        => $data['tin id'],
            ]
        );


        $this->createAccount(
                $data['employee no.'],
                $data['firstname'],
                $data['lastname'],
                $data['email'] ?? null
            );
            Log::info("Account created for employee", ['employee_no' =>  $data['employee no.']]);
    }

    Log::info('Employee Information upload completed');
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