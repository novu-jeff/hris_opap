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
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function PHPUnit\Framework\isEmpty;

class HRISProcessingService extends Controller
{

    public function save(bool $isFirstTime = false, string $employee_no, ?string $job_id = null, ?array $data = null) 
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
                    'email' => $record->email,
                    'birth_certificate' => $record->birth_certificate,
                    'marriage_certificate' => $record->marriage_certificate,
                ],
            ];

            $record = $this->employee_information($employee_no, $data , true);            
            $this->employee_account($record->employee_no, $data['employee_account'], true);
            $this->employee_personal($record->employee_no, $data['employee_personal'], true);

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

        $record = EmployeeInformation::where('employee_no', $employee_no)->first();

        $monthly_rate = $this->handleSalary($data);

        if ($record) {
            $record->fill([
            'section_id' => $data['section_id'] ? $data['section_id'] : null,
            'position_id' => $data['position_id'],
            'job_completion' => $data['job_completion'],
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

            return $record->save();
        }

        return false;
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

        $record = EmployeeAccount::where('employee_no', $employee_no)->first();

        $record->email = $data['email'];
        $record->save();

        if (isset($data['password'])) {
            $record->password = Hash::make($data['password']);
            return $record->save();
        }
    }

    public function employee_personal(string $employee_no, array $data, bool $isFirstTime = false)  {
        $path = 'documents/' . $employee_no;

        $birth_certificate = $this->uploadFile($employee_no, 'birth_certificate', $path,  $data['birth_certificate'] ?? null);
        $marriage_certificate = $this->uploadFile($employee_no, 'marriage_certificate', $path, $data['marriage_certificate'] ?? null);

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
            'birth_certificate' => $birth_certificate,
            'marriage_certificate' => $marriage_certificate,
            'solo_parent' => isset($data['solo_parent']) && strtolower($data['solo_parent']) === 'yes' ? true : false,
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

        $record = EmployeePersonal::where('employee_no', $employee_no)->first();
        if ($record) {
            $record->fill($template);
            return $record->save();
        }

        return false;
    }

    public function employee_parents(string $employee_no, ?array $data = null, bool $isFirstTime = false) {

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

        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeChildren::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeChildren::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeChildren::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'firstname' => $item['firstname'],
                            'middlename' => $item['middlename'],
                            'lastname' => $item['lastname'],
                            'birthdate' => $item['birthdate'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeChildren::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'firstname' => $item['firstname'],
                            'middlename' => $item['middlename'],
                            'lastname' => $item['lastname'],
                            'birthdate' => $item['birthdate'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeChildren::create([
                    'employee_no' => $employee_no,
                    'firstname' => $item['firstname'],
                    'middlename' => $item['middlename'],
                    'lastname' => $item['lastname'],
                    'birthdate' => $item['birthdate'],
                    'documents' => $documents
                ]);
            }
        }
    }

    public function employee_education(string $employee_no, array $data) {

        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeEducation::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeEducation::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeEducation::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'level' => $item['level'],
                            'school_name' => $item['school_name'],
                            'course' => $item['course'],
                            'from_year' => $item['from_year'],
                            'to_year' => $item['to_year'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeEducation::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'level' => $item['level'],
                            'school_name' => $item['school_name'],
                            'course' => $item['course'],
                            'from_year' => $item['from_year'],
                            'to_year' => $item['to_year'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeEducation::create([
                    'employee_no' => $employee_no,
                    'level' => $item['level'],
                    'school_name' => $item['school_name'],
                    'course' => $item['course'],
                    'from_year' => $item['from_year'],
                    'to_year' => $item['to_year'],
                    'documents' => $documents
                ]);
            }
        }

    }

    public function employee_employment_history(string $employee_no, array $data) {

        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeEmploymentHistory::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeEmploymentHistory::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeEmploymentHistory::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'position' => $item['position'],
                            'department' => $item['department'],
                            'company_name' => $item['company_name'],
                            'monthly_salary' => $item['monthly_salary'],
                            'employment_status' => $item['employment_status'],
                            'isGovernment' => $item['isGovernment'],
                            'from_year' => $item['from_year'],
                            'to_year' => $item['to_year'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeEmploymentHistory::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'position' => $item['position'],
                            'department' => $item['department'],
                            'company_name' => $item['company_name'],
                            'monthly_salary' => $item['monthly_salary'],
                            'employment_status' => $item['employment_status'],
                            'isGovernment' => $item['isGovernment'],
                            'from_year' => $item['from_year'],
                            'to_year' => $item['to_year'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeEmploymentHistory::create([
                    'employee_no' => $employee_no,
                    'position' => $item['position'],
                    'department' => $item['department'],
                    'company_name' => $item['company_name'],
                    'monthly_salary' => $item['monthly_salary'],
                    'employment_status' => $item['employment_status'],
                    'isGovernment' => $item['isGovernment'],
                    'from_year' => $item['from_year'],
                    'to_year' => $item['to_year'],
                    'documents' => $documents
                ]);
            }
        }

    }

    public function employee_civil_service(string $employee_no, array $data) {


        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeCivilService::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeCivilService::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeCivilService::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'certification' => $item['certification'],
                            'rating' => $item['rating'],
                            'date_exam' => $item['date_exam'],
                            'place_exam' => $item['place_exam'],
                            'license_no' => $item['license_no'],
                            'date_validity' => $item['date_validity'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeCivilService::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'certification' => $item['certification'],
                            'rating' => $item['rating'],
                            'date_exam' => $item['date_exam'],
                            'place_exam' => $item['place_exam'],
                            'license_no' => $item['license_no'],
                            'date_validity' => $item['date_validity'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeCivilService::create([
                    'employee_no' => $employee_no,
                    'certification' => $item['certification'],
                    'rating' => $item['rating'],
                    'date_exam' => $item['date_exam'],
                    'place_exam' => $item['place_exam'],
                    'license_no' => $item['license_no'],
                    'date_validity' => $item['date_validity'],
                    'documents' => $documents
                ]);
            }
        }
    }

    public function employee_trainings(string $employee_no, array $data) {


        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeTrainings::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeTrainings::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeTrainings::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'type' => $item['type'],
                            'name' => $item['name'],
                            'date_from' => $item['date_from'],
                            'date_to' => $item['date_to'],
                            'consumed_hours' => $item['consumed_hours'],
                            'sponsored_by' => $item['sponsored_by'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeTrainings::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'type' => $item['type'],
                            'name' => $item['name'],
                            'date_from' => $item['date_from'],
                            'date_to' => $item['date_to'],
                            'consumed_hours' => $item['consumed_hours'],
                            'sponsored_by' => $item['sponsored_by'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeTrainings::create([
                    'employee_no' => $employee_no,
                    'type' => $item['type'],
                    'name' => $item['name'],
                    'date_from' => $item['date_from'],
                    'date_to' => $item['date_to'],
                    'consumed_hours' => $item['consumed_hours'],
                    'sponsored_by' => $item['sponsored_by'],
                    'documents' => $documents
                ]);
            }
        }
    }

    public function employee_others(string $employee_no, array $data) {

        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeOtherWorks::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeOtherWorks::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeOtherWorks::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'organization' => $item['organization'],
                            'address' => $item['address'],
                            'date_from' => $item['date_from'],
                            'date_to' => $item['date_to'],
                            'consumed_hours' => $item['consumed_hours'],
                            'position' => $item['position'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeOtherWorks::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'organization' => $item['organization'],
                            'address' => $item['address'],
                            'date_from' => $item['date_from'],
                            'date_to' => $item['date_to'],
                            'consumed_hours' => $item['consumed_hours'],
                            'position' => $item['position'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeOtherWorks::create([
                    'employee_no' => $employee_no,
                    'organization' => $item['organization'],
                    'address' => $item['address'],
                    'date_from' => $item['date_from'],
                    'date_to' => $item['date_to'],
                    'consumed_hours' => $item['consumed_hours'],
                    'position' => $item['position'],
                    'documents' => $documents
                ]);
            }
        }
    }

    public function employee_skills(string $employee_no, array $data) {

        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeSkillsHobbies::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeSkillsHobbies::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeSkillsHobbies::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'name' => $item['name'],
                            'recognition' => $item['recognition'],
                            'organization' => $item['organization'],
                            'documents' => $documents
                        ])->save();
                    }
                } else {
                    $record = EmployeeSkillsHobbies::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'name' => $item['name'],
                            'recognition' => $item['recognition'],
                            'organization' => $item['organization'],
                        ])->save();
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeSkillsHobbies::create([
                    'employee_no' => $employee_no,
                    'name' => $item['name'],
                    'recognition' => $item['recognition'],
                    'organization' => $item['organization'],
                    'documents' => $documents
                ]);
            }
        }

    }

    public function handleSalary(array $data) {
        $eligible = $data['type'];
        $position_id = $data['position_id'];
        $step_id = $data['step_id'];

        if(in_array($data['type'], [1,2])) {
            if ($position_id && $step_id) {
                $salaryGrade = Positions::where('id', $position_id)
                    ->value('salary_grade') ?? '';
    
                $stepColumn = "step_" . ($step_id ?? '');
    
                $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn, $eligible) {
                        $query->where('salary_grade', $salaryGrade)
                            ->select('id', 'tranche_id', 'salary_grade', $stepColumn);
                    }])
                    ->where('eligible', $eligible)
                    ->first();
                
                $salary = ($activeTranche && $activeTranche->items->isNotEmpty()) 
                    ? $activeTranche->items->first()->$stepColumn 
                    : 0;
    
                if ($activeTranche) {
                    return $data['monthly_rate'] = $salary;
                }
            }
        } else {
            return $data['monthly_rate'];
        }
        
    }

    private function uploadFile($employee_no, $identifier, $path, $file)
    {
        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $record = EmployeePersonal::with([
                'children', 'employment_history', 'civil_service', 'trainings', 'others', 'skills'
            ])->where('employee_no', $employee_no)->first();
        
            if ($record) {
                if (!empty($record->$identifier)) {
                    Storage::disk('public')->delete("$path/{$record->$identifier}");
                }
        
                if ($identifier === 'documents') {
                    foreach (['children', 'employment_history', 'civil_service', 'trainings', 'others', 'skills'] as $relation) {
                        if ($record->$relation && !empty($record->$relation->$identifier)) {
                            Storage::disk('public')->delete("$path/{$record->$relation->$identifier}");
                        }
                    }
                }
            }
            
            $filename = uniqid(time()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $filename, 'public');
        
            return $filename;
        }
        
        $record = EmployeePersonal::where('employee_no', $employee_no)->first();
        return $record->$identifier ?? null;
        
    }
    

}