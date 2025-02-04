<?php

namespace App\Http\Controllers\Admin\Services;

use App\Helper\Generate;
use App\Http\Controllers\Controller;
use App\Mail\SendEmployeeAccount;
use App\Models\ApplicantUsers;
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
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\Positions;
use App\Models\Tranche;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;

class HRISProcessingService extends Controller
{

    public function save(bool $isFirstTime = false, string $employee_no, string $job_id = null, array $data = null) 
    {    

        $record = ApplicantUsers::with('applied.offer')
            ->whereHas('applied', fn($query) => $query->where('id', $job_id))
            ->find($employee_no);
        

        if ($isFirstTime && $record) {

            $data = [
                'employee_information' => [
                    'salary' => $record->applied[0]->offer->salary
                ],
                'employee_account' => [
                    'applicant_id' => $record->id,
                    'email' => $record->email,
                    'firstname' => $record->firstname,
                    'lastname' => $record->lastname,
                ],
                'employee_personal' => [
                    'profile' => $record->image,
                    'firstname' => $record->firstname,
                    'middlename' => $record->middlename,
                    'lastname' => $record->lastname,
                    'birthday' => $record->birthday,
                    'civil_status' => $record->civil_status,
                    'sex' => $record->sex,
                    'mobile_number' => $record->phone_no,
                    'tel_no' => $record->tel_no,
                    'email' => $record->email
                ],
            ];

            $record = $this->employee_information($employee_no, $data , true);            
            $this->employee_account($record->employee_no, $data['employee_account'], true);
            $this->employee_personal($record->employee_no, $data['employee_personal'], true);
            $this->employee_parents($record->employee_no, null, true);
            $this->employee_leave($record->employee_no, $data);

        } else {

            $data['employee_account']['email'] = $data['employee_personal']['email'];

            $this->employee_information($employee_no, $data['employee_information'], false);
            $this->employee_account($employee_no, $data['employee_account'], false);
            
            $this->employee_personal($employee_no, $data['employee_personal'], false);            
            $this->employee_parents($employee_no, $data['employee_parents'], false);
            $this->employee_children($employee_no, $data['employee_children']);
            $this->employee_education($employee_no, $data['employee_education']);
            $this->employee_employment_history($employee_no, $data['employee_employment_history']);
            
            $this->employee_civil_service($employee_no, $data['employee_civil_service']);
            $this->employee_trainings($employee_no, $data['employee_trainings']);
            $this->employee_others($employee_no, $data['employee_others']);
            $this->employee_skills($employee_no, $data['employee_skills']);

            $this->employee_leave($employee_no, $data);

        }
    }

    public function employee_information(string $employee_no, array $data, bool $isFirstTime = false)  {
        
        if ($isFirstTime) {

            $generate = new Generate;

            $employee_no = $generate->employee_no();
            
            $record = EmployeeInformation::create([
                'employee_no' => $employee_no,
                'bsd_no' => $data['employee_information']['biometrics_id'] ?? '',
                'date_hired' => Carbon::now()->format('Y-m-d'),
            ]);

            return $record;
        }

        $record = EmployeeInformation::where('employee_no', $employee_no);

        $monthly_rate = $this->handleSalary($data);

        return $record->update([
            'section_id' => $data['section_id'] ? $data['section_id'] : null,
            'position_id' => $data['position_id'],
            'bsd_no' => $data['biometrics_id'],
            'shift_id' => $data['shift_schedule'] ? $data['shift_schedule'] : null,
            'schedule_id' => $data['employee_schedule'] ? $data['employee_schedule'] : null,
            'employee_no' => $data['employee_no'],
            'date_resignation' => $data['date_resignation'] ?? null,
            'employment_type_id' => $data['type'],
            'status' => $data['status'],
            'salary_method' => $data['salary_method'],
            'monthly_rate' => $monthly_rate,
            'payroll_account_number' => $data['payroll_account_number'],
        ]);
    }

    public function employee_account(string $employee_no, array $data, bool $isFirstTime = false)  {

        if ($isFirstTime) {

            $generate = new Generate;

            $applicant_id = $data['applicant_id'];
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];
            $email = $data['email'];

            $email_id = $generate->email($employee_no, $firstname, $lastname);

            $record = EmployeeAccount::create([
                'employee_no' => $employee_no,
                'applicant_id' => $applicant_id,
                'email_id' => $email_id,
                'email' => $email
            ]);

            $record->assignRole('employee');

            return $record;
        }

        $record = EmployeeAccount::where('employee_no', $employee_no);

        $record->update([
            'email' => $data['email']
        ]);

        if(isset($data['password'])) {
            return $record->update([
                'password' => Hash::make($data['password']),
            ]);
        }
    }

    public function employee_personal(string $employee_no, array $data, bool $isFirstTime = false)  {
        
        $template = [
            'employee_no' => $employee_no,
            'profile' => !empty($data['profile']) ? $data['profile'] : null,
            'firstname' => !empty($data['firstname']) ? $data['firstname'] : null,
            'middlename' => !empty($data['middlename']) ? $data['middlename'] : null,
            'lastname' => !empty($data['lastname']) ? $data['lastname'] : null,
            'suffix' => !empty($data['suffix']) ? $data['suffix'] : null,
            'birthday' => !empty($data['birthday']) ? $data['birthday'] : null,
            'civil_status' => !empty($data['civil_status']) ? $data['civil_status'] : null,
            'sex' => !empty($data['sex']) ? $data['sex'] : null,
            'citizenship' => !empty($data['citizenship']) ? $data['citizenship'] : null,
            'citizenship_type' => !empty($data['citizenship_type']) ? $data['citizenship_type'] : null,
            'country' => !empty($data['country']) ? $data['country'] : null,
            'present_address' => !empty($data['present_address']) ? $data['present_address'] : null,
            'present_province' => !empty($data['present_province']) ? $data['present_province'] : null,
            'present_city' => !empty($data['present_city']) ? $data['present_city'] : null,
            'permanent_address' => !empty($data['permanent_address']) ? $data['permanent_address'] : null,
            'permanent_province' => !empty($data['permanent_province']) ? $data['permanent_province'] : null,
            'permanent_city' => !empty($data['permanent_city']) ? $data['permanent_city'] : null,
            'mobile_number' => !empty($data['mobile_number']) ? $data['mobile_number'] : null,
            'tel_no' => !empty($data['tel_no']) ? $data['tel_no'] : null,
            'height' => !empty($data['height']) ? $data['height'] : null,
            'weight' => !empty($data['weight']) ? $data['weight'] : null,
            'blood_type' => !empty($data['blood_type']) ? $data['blood_type'] : null,
            'gsis_no' => !empty($data['gsis_no']) ? $data['gsis_no'] : null,
            'pagibig_no' => !empty($data['pagibig_no']) ? $data['pagibig_no'] : null,
            'philhealth_no' => !empty($data['philhealth_no']) ? $data['philhealth_no'] : null,
            'sss_no' => !empty($data['sss_no']) ? $data['sss_no'] : null,
            'tin_no' => !empty($data['tin_no']) ? $data['tin_no'] : null,
        ];
        

        if ($isFirstTime) {
            $template['employee_no'] = $employee_no;
            return EmployeePersonal::create($template);
        }

        $record = EmployeePersonal::where('employee_no', $employee_no);
        return $record->update($template);
    }

    public function employee_parents(string $employee_no, array $data = null, bool $isFirstTime = false) {

        return EmployeeParents::updateOrCreate(
                [
                    'employee_no' => $employee_no
                ],
                [
                    'spouse_surname' => $data['spouse_surname'],
                    'spouse_firstname' => $data['spouse_firstname'],
                    'spouse_middlename' => $data['spouse_middlename'],
                    'spouse_suffix' => $data['spouse_suffix'],
                    'spouse_occupation' => $data['spouse_occupation'],
                    'spouse_business_name_employer' => $data['spouse_business_name_employer'],
                    'spouse_business_address' => $data['spouse_business_address'],
                    'spouse_contact_no' => $data['spouse_contact_no'],
                    'father_surname' => $data['father_surname'],
                    'father_firstname' => $data['father_firstname'],
                    'father_middlename' => $data['father_middlename'],
                    'father_suffix' => $data['father_suffix'],
                    'mother_surname' => $data['mother_surname'],
                    'mother_firstname' => $data['mother_firstname'],
                    'mother_middlename' => $data['mother_middlename'],
                ]);

    }

    public function employee_children(string $employee_no, array $data) {

        $record = EmployeeChildren::where('employee_no', $employee_no);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'firstname' => $value['firstname'],
                    'middlename' => $value['middlename'],
                    'lastname' => $value['lastname'],
                    'birthdate' => $value['birthdate'],
                ]);
            } 
        }

    }

    public function employee_education(string $employee_no, array $data) {

        $record = EmployeeEducation::where('employee_no', $employee_no);
        $record->delete();
    
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'level' => $value['level'],
                    'school_name' => $value['school_name'],
                    'course' => $value['course'],
                    'from_year' => $value['from_year'],
                    'to_year' => $value['to_year'],
                ]);
            } 
        }

    }

    public function employee_employment_history(string $employee_no, array $data) {

        $record = EmployeeEmploymentHistory::where('employee_no', $employee_no);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'position' => $value['position'],
                    'department' => $value['department'],
                    'company_name' => $value['company_name'],
                    'monthly_salary' => $value['monthly_salary'],
                    'employment_status' => $value['employment_status'],
                    'isGovernment' => $value['isGovernment'],
                    'from_year' => $value['from_year'],
                    'to_year' => $value['to_year'],
                ]);
            } 
        }

    }

    public function employee_civil_service(string $employee_no, array $data) {

        $record = EmployeeCivilService::where('employee_no', $employee_no);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'certification' => $value['certification'],
                    'rating' => $value['rating'],
                    'date_exam' => $value['date_exam'],
                    'place_exam' => $value['place_exam'],
                    'license_no' => $value['license_no'],
                    'date_validity' => $value['date_validity'],
                ]);
            } 
        }
    }

    public function employee_trainings(string $employee_no, array $data) {

        $record = EmployeeTrainings::where('employee_no', $employee_no);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'type' => $value['type'],
                    'name' => $value['name'],
                    'date_from' => $value['date_from'],
                    'date_to' => $value['date_to'],
                    'consumed_hours' => $value['consumed_hours'],
                    'sponsored_by' => $value['sponsored_by'],
                ]);
            } 
        }
    }

    public function employee_others(string $employee_no, array $data) {

        $record = EmployeeOtherWorks::where('employee_no', $employee_no);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'organization' => $value['organization'],
                    'address' => $value['address'],
                    'date_from' => $value['date_from'],
                    'date_to' => $value['date_to'],
                    'consumed_hours' => $value['consumed_hours'],
                    'position' => $value['position'],
                ]);
            } 
        }
    }

    public function employee_skills(string $employee_no, array $data) {

        $record = EmployeeSkillsHobbies::where('employee_no', $employee_no);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_no' => $employee_no,
                    'name' => $value['name'],
                    'recognition' => $value['recognition'],
                    'organization' => $value['organization'],
                ]);
            } 
        }
    }

    public function employee_leave(string $employee_no, array $data) {

        $leaveDefaultCredits = LeaveType::all();

        $model = LeaveCredits::class;

        $product = config('app.product');

        if ($product == 'opap') {
            if ($data['employee_information']['type'] == 1) {
                foreach ($leaveDefaultCredits as $leave) {

                    $credits = 0;
                    
                    $existingLeave = $model::where('employee_no', $employee_no)
                        ->where('leave_type_id', $leave->id)
                        ->first();
                        
                    if($existingLeave && $existingLeave->credits != 0) {
                        if ($leave->code == 'ML' && $data['employee_personal']['sex'] == 'female') {
                            $credits = $existingLeave->credits;
                        }

                        elseif ($leave->code == 'PL' && $data['employee_personal']['sex'] == 'male') {
                            $credits = $existingLeave->credits;
                        }

                        elseif ($leave->code == 'SOLO' || $leave->code == 'SPL') {
                            $credits = 0;
                        }

                        elseif ($leave->code !== 'PL' && $leave->code !== 'ML') {
                            $credits = $existingLeave->credits;
                        }
                    } else {
                        if ($leave->code == 'ML' && $data['employee_personal']['sex'] == 'female') {
                            $credits = $leave->credits;
                        }

                        elseif ($leave->code == 'PL' && $data['employee_personal']['sex'] == 'male') {
                            $credits = $leave->credits;
                        }

                        elseif ($leave->code == 'SOLO' || $leave->code == 'SPL') {
                            $credits = 0;
                        }

                        elseif ($leave->code !== 'PL' && $leave->code !== 'ML') {
                            $credits = $leave->credits;
                        }
                    }

                    
                
                    // Update or create the record with the appropriate credits
                    $model::updateOrCreate(
                        [
                            'employee_no' => $employee_no,
                            'leave_type_id' => $leave->id,
                        ],
                        [
                            'credits' => $credits,
                        ]
                    );
                }
                
                
            } else {
                foreach ($leaveDefaultCredits as $leave) {
                    $model::updateOrCreate(
                        [
                            'employee_no' => $employee_no,
                            'leave_type_id' => $leave->id,
                        ],
                        [
                            'credits' => 0,
                        ]
                    );
                }
            }
        }

    }

    public function handleSalary(array $data) {
        $position_id = $data['position_id'];
        $step_id = $data['step_id'];

        if ($position_id && $step_id) {
            $salaryGrade = Positions::where('id', $position_id)
                ->value('salary_grade') ?? '';

            $stepColumn = "step_" . ($step_id ?? '');

            $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn) {
                $query->where('salary_grade', $salaryGrade)
                    ->select('id', 'tranche_id', 'salary_grade', $stepColumn);
            }])
            ->where('isActive', true)
            ->first();

            $salary = $activeTranche->items->first()->$stepColumn ?? 0;

            if ($activeTranche) {
                $data['monthly_rate'] = $salary;
            }
        }
    }

}