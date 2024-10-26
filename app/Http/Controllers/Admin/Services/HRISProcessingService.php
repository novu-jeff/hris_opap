<?php

namespace App\Http\Controllers\Admin\Services;

use App\Helper\Generate;
use App\Http\Controllers\Controller;
use App\Mail\SendEmployeeAccount;
use App\Models\ApplicantUsers;
use App\Models\EmployeeAccount;
use App\Models\EmployeeChildren;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmploymentHistory;
use App\Models\EmployeeInformation;
use App\Models\EmployeeParents;
use App\Models\EmployeePersonal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class HRISProcessingService extends Controller
{

    public function save(bool $isFirstTime = false, int $id, array $data = null) 
    {

        $record = ApplicantUsers::find($id);

        if ($isFirstTime && $record) {

            $data = [
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
                ],
            ];

            $record = $this->employee_information($id, $data , true);            
            $this->employee_account($record->id, $data['employee_account'], true);
            $this->employee_personal($record->id, $data['employee_personal'], true);
            $this->employee_parents($record->id, null, true);

        } else {
            $this->employee_information($id, $data['employee_information'], false);
            // $this->employee_account($id, $data['employee_account'], false);
            
            $this->employee_personal($id, $data['employee_personal'], false);            
            $this->employee_parents($id, $data['employee_parents'], false);
            $this->employee_children($id, $data['employee_children']);
            $this->employee_education($id, $data['employee_education']);
            $this->employee_employment_history($id, $data['employee_employment_history']);
        }
    }

    public function employee_information(int $id, array $data, bool $isFirstTime = false)  {
        if ($isFirstTime) {
            $generate = new Generate;

            $record = EmployeeInformation::create([
                'biometrics_id' => $generate->biometrics(),
                'date_hired' => Carbon::now()->format('d F, Y'),
            ]);

            return $record;
        }

        $record = EmployeeInformation::find($id);

        return $record->update([
            'branch_id' => $data['branch_id'],
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'date_resignation' => $data['date_resignation'] ?? null,
            'type' => $data['type'],
            'status' => $data['status'],
            'salary_method' => $data['salary_method'],
            'leave_credits' => $data['leave_credits'],
            'monthly_rate' => $data['monthly_rate'],
            'payroll_account_number' => $data['payroll_account_number'],
        ]);
    }

    public function employee_account(int $id, array $data, bool $isFirstTime = false)  {
        
        if ($isFirstTime) {

            $generate = new Generate;

            $applicant_id = $data['applicant_id'];
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];

            $email = $generate->email($id, $firstname, $lastname);

            $record = EmployeeAccount::create([
                'employee_id' => $id,
                'applicant_id' => $applicant_id,
                'email' => $email,
            ]);

            return $record;
        }

        $record = EmployeeAccount::where('employee_id', $id);

        return $record->update([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }

    public function employee_personal(int $id, array $data, bool $isFirstTime = false)  {
        $template = [
            'profile' => $data['profile'] ?? null,
            'firstname' => $data['firstname'] ?? null,
            'middlename' => $data['middlename'] ?? null,
            'lastname' => $data['lastname'] ?? null,
            'suffix' => $data['suffix'] ?? null,
            'birthday' => $data['birthday'] ?? null,
            'civil_status' => $data['civil_status'] ?? null,
            'sex' => $data['sex'] ?? null,
            'citizenship' => $data['citizenship'] ?? null,
            'citizenship_type' => $data['citizenship_type'] ?? null,
            'country' => $data['country'] ?? null,
            'present_address' => $data['present_address'] ?? null,
            'present_province' => $data['present_province'] ?? null,
            'present_city' => $data['present_city'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'permanent_province' => $data['permanent_province'] ?? null,
            'permanent_city' => $data['permanent_city'] ?? null,
            'mobile_number' => $data['mobile_number'] ?? null,
            'tel_no' => $data['tel_no'] ?? null,
            'email' => $data['email'] ?? null,
            'height' => $data['height'] ?? null,
            'weight' => $data['weight'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'gsis_no' => $data['gsis_no'] ?? null,
            'pagibig_no' => $data['pagibig_no'] ?? null,
            'philhealth_no' => $data['philhealth_no'] ?? null,
            'sss_no' => $data['sss_no'] ?? null,
            'tin_no' => $data['tin_no'] ?? null,
        ];

        if ($isFirstTime) {
            $template['employee_id'] = $id;
            return EmployeePersonal::create($template);
        }

        $record = EmployeePersonal::where('employee_id', $id);
        return $record->update($template);
    }

    public function employee_parents(int $id, array $data = null, bool $isFirstTime = false) {

        if($isFirstTime) {
            return EmployeeParents::create([
                'employee_id' => $id
            ]);
        } 

        $record = EmployeeParents::where('employee_id', $id);

        return $record->update([
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

    public function employee_children(int $id, array $data) {

        $record = EmployeeChildren::where('employee_id', $id);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_id' => $id,
                    'firstname' => $value['firstname'],
                    'middlename' => $value['middlename'],
                    'lastname' => $value['lastname'],
                    'birthdate' => $value['birthdate'],
                ]);
            } 
        }

    }

    public function employee_education(int $id, array $data) {

        $record = EmployeeEducation::where('employee_id', $id);
        $record->delete();
    
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_id' => $id,
                    'level' => $value['level'],
                    'school_name' => $value['school_name'],
                    'course' => $value['course'],
                    'from_year' => $value['from_year'],
                    'to_year' => $value['to_year'],
                ]);
            } 
        }

    }

    public function employee_employment_history(int $id, array $data) {

        $record = EmployeeEmploymentHistory::where('employee_id', $id);
        $record->delete();
        
        if(!empty($data)) {
            foreach($data as $value) {
                $record->insert([
                    'employee_id' => $id,
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

}