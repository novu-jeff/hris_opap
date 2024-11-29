<?php

namespace App\Livewire\Admin\Hris;

use App\Helper\Generate;
use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Models\Branches;
use App\Models\Departments;
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
use App\Models\JobCategory;
use App\Models\Positions;
use App\Models\Sections;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Manual extends Component
{

    public $selected_id;
    public array $records;
    public object $positions;
    public object $sections;
    public object $jobCategories;
    public bool $isDualCitizenship = false;
    public array $countries;
    public string $activeTab = 'details';
    public $activeAccordion;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $this->sections = Sections::all();
        $this->positions = Positions::all();
        $this->jobCategories = JobCategory::all();
    }


    public function setActiveTab($tab) {
        $this->activeTab = $tab;
    }

    public function setActiveAccordion($accordion) {
        if($this->activeAccordion !== $accordion) {
            $this->activeAccordion = $accordion;
        } else {
            $this->activeAccordion = '';
        }
    }

    public function select_change(string $property) {
        
        if($property == 'citizenship') {
            $this->setActiveAccordion('personal');
            if($this->records['employee_personal']['citizenship'] == 'dual_citizenship') {
                $this->isDualCitizenship = true;
            } else {
                $this->isDualCitizenship = false;
            }
        }

        if($property == 'section') {
            $section_id = $this->records['employee_information']['section_id'];
            $record = Sections::with('branch', 'department')->where('id', $section_id)->first();
            
            if($record) {
                $this->records['employee_information']['branch'] = $record->branch->name ?? '';
                $this->records['employee_information']['department'] = $record->department->name ?? '';
            }
        }
    }

    public function addRecord($tab, $type, $accordion = null) {

        $this->activeAccordion = $accordion;

        $fields = [
            'employee_education' => [
                'level' => '',
                'school_name' => '',
                'course' => '',
                'from_year' => '',
                'to_year' => '',
            ],
            'employee_employment_history' => [
                'position' => '',
                'department' => '',
                'company_name' => '',
                'monthly_salary' => '',
                'employment_status' => '',
                'isGovernment' => '',
                'from_year' => '',
                'to_year' => ''
            ],
            'employee_children' => [
                'firstname' => '',
                'middlename' => '',
                'lastname' => '',
                'birthdate' => '',
            ],
            'employee_civil_service' => [],
            'employee_trainings' => [],
            'employee_others' => [],
            'employee_skills' => [],
        ];
    
        $this->records[$type][] = $fields[$type];
    }

    public function removeRecord($tab, $type, $index) {
        $this->activeTab = $tab;
        
        if (isset($this->records[$type][$index])) {
            unset($this->records[$type][$index]);
            $this->records[$type] = array_values($this->records[$type]);
        }
    }
    
    public function setErrorActiveTabAccordions(array $errorKeys) {
        foreach ($errorKeys as $key) {
            $parts = explode('.', $key);
            
            if (isset($parts[1])) {
                switch ($parts[1]) {
                    case 'employee_personal':
                        $this->activeTab = 'details';
    
                        $accordion = [
                            'personal' => [
                                'firstname',
                                'lastname',
                                'middlename',
                                'suffix',
                                'birthday',
                                'civil_status',
                                'sex',
                                'citizenship',
                                'citizenship_type'
                            ],
                            'address' => [
                                'present_address',
                                'present_province',
                                'present_city',
                                'permanent_address',
                                'permanent_province',
                                'permanent_city'
                            ],
                            'contact' => [
                                'mobile_number',
                                'tel_no',
                                'company_email'
                            ],
                            'appearance' => [
                                'height',
                                'weight',
                                'blood_type'
                            ],
                            'identification' => [
                                'gsis_no',
                                'pagibig_no',
                                'philhealth_no',
                                'sss_no',
                                'tin_no'
                            ]
                        ];
    
                        $lastKey = end($parts); // Get the last part of the error key
                        $this->activeAccordion = $this->findAccordionKey($lastKey, $accordion);
                        
                        return;
    
                    case 'employee_children':
                        $this->activeTab = 'family';
    
                        $accordion = [
                            'parents' => [
                                'spouse_surname',
                                'spouse_firstname',
                                'spouse_middlename',
                                'spouse_suffix',
                                'spouse_occupation',
                                'spouse_business_name_employer',
                                'spouse_business_address',
                                'spouse_contact_no',
                                'father_surname',
                                'father_firstname',
                                'father_middlename',
                                'father_suffix',
                                'mother_surname',
                                'mother_firstname',
                                'mother_middlename',
                            ],
                            'children' => [
                                'firstname',
                                'lastname',
                                'middlename',
                                'birthdate'
                            ]
                        ];
    
                        $lastKey = end($parts);
                        $this->activeAccordion = $this->findAccordionKey($lastKey, $accordion);
                        return;
    
                    case 'employee_education':
                        $this->activeTab = 'education';
                        return;
    
                    case 'employee_employment_history':
                        $this->activeTab = 'history';
                        return;

                    case 'employee_civil_service':
                        $this->activeTab = 'civil_service';
                        return;

                    case 'employee_trainings':
                        $this->activeTab = 'trainings';
                        return;

                    case 'employee_others':
                        $this->activeTab = 'others';
                        return;
    
                    case 'employee_skills':
                        $this->activeTab = 'skills';
                        return;

                    case 'employee_account':
                        $this->activeTab = 'account';
                        return;
                }
            }
        }
    }

    private function findAccordionKey(string $key, array $accordion) {
        foreach ($accordion as $accordionKey => $fields) {
            if (in_array($key, $fields)) {
                return $accordionKey;
            }
        }
        return null;
    }

    protected function rules(int $id = null) {
        return [
            'records.employee_information.employee_no' => [
                'required',
                Rule::unique('employee_information', 'employee_no')->ignore($id, 'employee_no')
            ],
            'records.employee_information.type' => 'nullable|exists:job_categories,id',
            'records.employee_information.status' => 'nullable|in:active,inactive',
            'records.employee_information.date_hired' => 'required|date',
            'records.employee_information.position_id' => 'nullable|exists:positions,id',
            'records.employee_information.section_id' => 'required|exists:sections,id',
            'records.employee_information.monthly_rate' => 'required|numeric|gt:1000',
            'records.employee_information.salary_method' => 'nullable|in:cash,bank transfer,paycheck,e-wallet',
            'records.employee_information.biometrics_id' => 'nullable|numeric',
            'records.employee_information.type' => 'required|exists:job_categories,id',


            'records.employee_personal.firstname' => 'required|string|max:255',
            'records.employee_personal.lastname' => 'required|string|max:255',
            'records.employee_personal.suffix' => 'nullable|in:jr,sr,I,II,III,IV,V',
            'records.employee_personal.civil_status' => 'nullable|in:single,married,divorced,seperated,widowed,anulled',
            'records.employee_personal.sex' => 'nullable|in:male,female',
            'records.employee_personal.citizenship_type' => 'nullable|required_with:records.employee_personal.citizenship',
            'records.employee_personal.country' => 'required_if:records.employee_personal.citizenship,dual_citizenship',

            'records.employee_personal.mobile_number' => 'nullable|regex:/^09\d{9}$/',
            'records.employee_personal.email' => 'nullable|email',


            'records.employee_children.*.firstname' => 'required|string|max:255',
            'records.employee_children.*.middlename' => 'nullable|string|max:255',
            'records.employee_children.*.lastname' => 'required|string|max:255',
            'records.employee_children.*.birthdate' => 'required|date',

            'records.employee_education.*.level' => 'required|string',
            'records.employee_education.*.school_name' => 'required|string|max:255',
            'records.employee_education.*.course' => 'required|string|max:255',
            'records.employee_education.*.from_year' => 'required|date',
            'records.employee_education.*.to_year' => 'required|date',

            'records.employee_employment_history.*.position' => 'required|string|max:255',
            'records.employee_employment_history.*.department' => 'required|string|max:255',
            'records.employee_employment_history.*.company_name' => 'required|string|max:255',
            'records.employee_employment_history.*.monthly_salary' => 'required|numeric|min:0',
            'records.employee_employment_history.*.employment_status' => 'required|string',
            'records.employee_employment_history.*.isGovernment' => 'required|string',
            'records.employee_employment_history.*.from_year' => 'required|date',
            'records.employee_employment_history.*.to_year' => 'required|date|after_or_equal:records.employee_employment_history.*.from_year',

            'records.employee_civil_service.*.certification' => 'required|string|max:255',
            'records.employee_civil_service.*.rating' => 'required|string|max:255',
            'records.employee_civil_service.*.date_exam' => 'required|string|max:255',
            'records.employee_civil_service.*.place_exam' => 'required|string|max:255',
            'records.employee_civil_service.*.license_no' => 'required|string|max:255',
            'records.employee_civil_service.*.date_validity' => 'required|date',

            'records.employee_trainings.*.type' => 'required|string|max:255',
            'records.employee_trainings.*.name' => 'required|string|max:255',
            'records.employee_trainings.*.date_from' => 'required|string|max:255',
            'records.employee_trainings.*.date_to' => 'required|string|max:255',
            'records.employee_trainings.*.consumed_hours' => 'required|integer',
            'records.employee_trainings.*.sponsored_by' => 'required|string|max:255',

            'records.employee_others.*.organization' => 'required|string|max:255',
            'records.employee_others.*.address' => 'required|string|max:255',
            'records.employee_others.*.date_from' => 'required|string|max:255',
            'records.employee_others.*.date_to' => 'required|string|max:255',
            'records.employee_others.*.consumed_hours' => 'required|integer',
            'records.employee_others.*.position' => 'required|string|max:255',

            'records.employee_skills.*.name' => 'required|string|max:255',
            'records.employee_skills.*.recognition' => 'required|string|max:255',
            'records.employee_skills.*.organization' => 'required|string|max:255',


            'records.employee_account.password' => 'nullable|min:8|same:records.employee_account.confirm_password',
            'records.employee_account.confirm_password' => 'required_with:records.employee_account.password|min:8'

        ];
    }

    public function messages() {
        return [
            'records.employee_information.employee_no.required' => 'The employee no is required.',
            'records.employee_information.employee_no.unique' => 'The employee no is already taken.',
            'records.employee_information.type.in' => 'The selected employment type does not exists.',
            'records.employee_information.status.in' => 'The status must be either active or inactive.',
            'records.employee_information.date_hired.required' => 'The date hired is required',
            'records.employee_information.date_hired.date' => 'The date hired must be valid date',
            'records.employee_information.monthly_rate.required' => 'The monthly rate is required',
            'records.employee_information.monthly_rate.numeric' => 'The monthly rate must be numbers',
            'records.employee_information.monthly_rate.gt' => 'The monthly rate must be greather than 1000',
            'records.employee_information.section_id.required' => 'The section is required.',
            'records.employee_information.section_id.exists' => 'The selected section does not exist.',
            'records.employee_information.position_id.exists' => 'The selected position does not exist.',
            'records.employee_information.salary_method.in' => 'The salary method must be one of the following: cash, bank transfer, paycheck, or e-wallet.',
            'records.employee_information.type.required' => 'The employment type is required',
            'records.employee_information.type.exists' => 'The selected employment type does not exists.',

            'records.employee_personal.firstname.required' => 'The first name is required.',
            'records.employee_personal.lastname.required' => 'The last name is required.',
            'records.employee_personal.suffix.in' => 'The suffix must be one of the following: jr, sr, I, II, III, IV, or V.',
            'records.employee_personal.civil_status.in' => 'The civil status must be one of the following: single, married, divorced, separated, widowed, or annulled.',
            'records.employee_personal.sex.in' => 'The sex must be either male or female.',
            'records.employee_personal.citizenship_type.required_with' => 'The citizenship type is required when citizenship is provided.',
            'records.employee_personal.country.required_if' => 'The country is required when citizenship is dual citizenship.',

            'records.employee_personal.mobile_number.regex' => 'The mobile number format is invalid. It should start with 09 and be followed by 9 digits.',
            'records.employee_personal.email.email' => 'The email must be a valid email address.',

            'records.employee_children.*.firstname.required' => 'Each child must have a first name.',
            'records.employee_children.*.middlename.string' => 'The middle name must be a string.',
            'records.employee_children.*.lastname.required' => 'Each child must have a last name.',
            'records.employee_children.*.birthdate.required' => 'The birthdate is required for each child.',
            'records.employee_children.*.birthdate.date' => 'The birthdate must be a valid date.',

            'records.employee_education.*.level.required' => 'The education level is required.',
            'records.employee_education.*.school_name.required' => 'The school name is required.',
            'records.employee_education.*.course.required' => 'The course name is required.',
            'records.employee_education.*.from_year.required' => 'The start year is required.',
            'records.employee_education.*.to_year.required' => 'The end year is required.',
            'records.employee_education.*.to_year.after_or_equal' => 'The end year must be the same or after the start year.',

            'records.employee_employment_history.*.position.required' => 'The position is required for each employment history entry.',
            'records.employee_employment_history.*.department.required' => 'The department is required for each employment history entry.',
            'records.employee_employment_history.*.company_name.required' => 'The company name is required for each employment history entry.',
            'records.employee_employment_history.*.monthly_salary.required' => 'The monthly salary is required.',
            'records.employee_employment_history.*.monthly_salary.numeric' => 'The monthly salary must be a number.',
            'records.employee_employment_history.*.employment_status.required' => 'The employment status is required.',
            'records.employee_employment_history.*.isGovernment.required' => 'The field indicating government employment is required.',
            'records.employee_employment_history.*.from_year.required' => 'The start date is required.',
            'records.employee_employment_history.*.to_year.required' => 'The end date is required.',
            'records.employee_employment_history.*.to_year.after_or_equal' => 'The end date must be on or after the start date for each employment history entry.',
            
            'records.employee_civil_service.*.certification.required' => 'The certification field is required for each civil service record.',
            'records.employee_civil_service.*.rating.required' => 'The rating field is required for each civil service record.',
            'records.employee_civil_service.*.date_exam.required' => 'The date of the exam is required for each civil service record.',
            'records.employee_civil_service.*.place_exam.required' => 'The place of the exam is required for each civil service record.',
            'records.employee_civil_service.*.license_no.required' => 'The license number is required for each civil service record.',
            'records.employee_civil_service.*.date_validity.required' => 'The date of validity is required for each civil service record.',
            'records.employee_civil_service.*.date_validity.date' => 'The date validity must be a valid date for each civil service record.',

            'records.employee_trainings.*.type.required' => 'The training type is required for each training record.',
            'records.employee_trainings.*.name.required' => 'The training name is required for each training record.',
            'records.employee_trainings.*.date_from.required' => 'The start date is required for each training record.',
            'records.employee_trainings.*.date_to.required' => 'The end date is required for each training record.',
            'records.employee_trainings.*.consumed_hours.required' => 'The consumed hours field is required for each training record.',
            'records.employee_trainings.*.consumed_hours.integer' => 'The consumed hours must be a valid integer for each training record.',
            'records.employee_trainings.*.sponsored_by.required' => 'The sponsored by field is required for each training record.',

            'records.employee_others.*.organization.required' => 'The organization field is required for each other record.',
            'records.employee_others.*.address.required' => 'The address field is required for each other record.',
            'records.employee_others.*.date_from.required' => 'The start date is required for each other record.',
            'records.employee_others.*.date_to.required' => 'The end date is required for each other record.',
            'records.employee_others.*.consumed_hours.required' => 'The consumed hours field is required for each other record.',
            'records.employee_others.*.consumed_hours.integer' => 'The consumed hours must be a valid integer for each other record.',
            'records.employee_others.*.position.required' => 'The position field is required for each other record.',

            'records.employee_skills.*.name.required' => 'The skill name field is required for each skill record.',
            'records.employee_skills.*.recognition.required' => 'The recognition field is required for each skill record.',
            'records.employee_skills.*.organization.required' => 'The organization field is required for each skill record.',  
            
            'records.employee_account.password.max' => 'The password must be at least 8 characters.',
            'records.employee_account.password.same' => 'The password and confirmation password must match.',
            'records.employee_account.confirm_password.required_with' => 'The confirm password field is required.',
            'records.employee_account.confirm_password.max' => 'The confirm password must be at least 8 characters.',
        ];
    }

    public function save() {

        try {
            $this->validate($this->rules());
        } catch (ValidationException $e) {
            $this->setErrorActiveTabAccordions($e->validator->errors()->keys());
            throw $e; 
        }
                
        DB::beginTransaction();

        try {
            
            $record = $this->employee_information($this->records['employee_information']);
            $this->employee_account($record->employee_no, $this->records['employee_personal'] ?? []);
            $this->employee_personal($record->employee_no, $this->records['employee_personal'] ?? []);
            $this->employee_parents($record->employee_no, $this->records['employee_parents'] ?? []);
            $this->employee_children($record->employee_no, $this->records['employee_children'] ?? []);
            $this->employee_education($record->employee_no, $this->records['employee_education'] ?? []);
            $this->employee_employment_history($record->employee_no, $this->records['employee_employment_history'] ?? []);
            $this->employee_civil_service($record->employee_no, $this->records['employee_civil_service'] ?? []);
            $this->employee_trainings($record->employee_no, $this->records['employee_trainings'] ?? []);
            $this->employee_others($record->employee_no, $this->records['employee_others'] ?? []);
            $this->employee_skills($record->employee_no, $this->records['employee_skills'] ?? []);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee #' . $record->employee_no . ' records successfully.',
                'redirect' => route('hris.manual')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage() 
            ]);
        }
    }

    public function employee_information(array $data) {
        return EmployeeInformation::create([
            'employee_no' => $data['employee_no'] ?? null,
            'section_id' => $data['section_id'] ?? null,
            'position_id' => $data['position_id'] ?? null,
            'date_hired' => $data['date_hired'] ?? null,
            'biometrics_id' => $data['biometrics_id'] ?? null,
            'date_resignation' => $data['date_resignation'] ?? null,
            'job_category_id' => $data['type'] ?? null,
            'status' => $data['status'] ?? null,
            'salary_method' => $data['salary_method'] ?? null,
            'leave_credits' => $data['leave_credits'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? null,
            'payroll_account_number' => $data['payroll_account_number'] ?? null,
        ]);
    }

    public function employee_account(string $employee_no, array $data) {

        $generate = new Generate;

        $applicant_id = $data['applicant_id'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $generate->email($employee_no, $firstname, $lastname);
        
        return EmployeeAccount::create([
            'employee_no' => $employee_no,
            'applicant_id' => $applicant_id,
            'email' => $email,
        ]);

    }

    public function employee_personal(string $employee_no, array $data) {
        return EmployeePersonal::create([
            'employee_no' => $employee_no,
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
        ]);        
    }

    public function employee_parents(string $employee_no, array $data) {
        return EmployeeParents::create([
            'employee_no' => $employee_no,
            'spouse_surname' => $data['spouse_surname'] ?? null,
            'spouse_firstname' => $data['spouse_firstname'] ?? null,
            'spouse_middlename' => $data['spouse_middlename'] ?? null,
            'spouse_suffix' => $data['spouse_suffix'] ?? null,
            'spouse_occupation' => $data['spouse_occupation'] ?? null,
            'spouse_business_name_employer' => $data['spouse_business_name_employer'] ?? null,
            'spouse_business_address' => $data['spouse_business_address'] ?? null,
            'spouse_contact_no' => $data['spouse_contact_no'] ?? null,
            'father_surname' => $data['father_surname'] ?? null,
            'father_firstname' => $data['father_firstname'] ?? null,
            'father_middlename' => $data['father_middlename'] ?? null,
            'father_suffix' => $data['father_suffix'] ?? null,
            'mother_surname' => $data['mother_surname'] ?? null,
            'mother_firstname' => $data['mother_firstname'] ?? null,
            'mother_middlename' => $data['mother_middlename'] ?? null,
        ]);
    }

    public function employee_children(string $employee_no, array $data) {
        $model = EmployeeChildren::class;
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'firstname' => $value['firstname'] ?? null,
                    'middlename' => $value['middlename'] ?? null,
                    'lastname' => $value['lastname'] ?? null,
                    'birthdate' => $value['birthdate'] ?? null,
                ]);
            } 
        }
    }

    public function employee_education(string $employee_no, array $data) {
        $model = EmployeeEducation::class;
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'level' => $value['level'] ?? null,
                    'school_name' => $value['school_name'] ?? null,
                    'course' => $value['course'] ?? null,
                    'from_year' => $value['from_year'] ?? null,
                    'to_year' => $value['to_year'] ?? null,
                ]);
            } 
        }
    }

    public function employee_employment_history(string $employee_no, array $data) {
        $model = EmployeeEmploymentHistory::class;
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'position' => $value['position'] ?? null,
                    'department' => $value['department'] ?? null,
                    'company_name' => $value['company_name'] ?? null,
                    'monthly_salary' => $value['monthly_salary'] ?? null,
                    'employment_status' => $value['employment_status'] ?? null,
                    'isGovernment' => $value['isGovernment'] ?? null,
                    'from_year' => $value['from_year'] ?? null,
                    'to_year' => $value['to_year'] ?? null,
                ]);
            } 
        }
    }

    public function employee_civil_service(string $employee_no, array $data) {
        $model = EmployeeCivilService::class;
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'certification' => $value['certification'] ?? null,
                    'rating' => $value['rating'] ?? null,
                    'date_exam' => $value['date_exam'] ?? null,
                    'place_exam' => $value['place_exam'] ?? null,
                    'license_no' => $value['license_no'] ?? null,
                    'date_validaity' => $value['date_validaity'] ?? null,
                ]);
            } 
        }
    }

    public function employee_trainings(string $employee_no, array $data) {
        $model = EmployeeTrainings::class;
        
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'type' => $value['type'] ?? null,
                    'name' => $value['name'] ?? null,
                    'date_from' => $value['date_from'] ?? null,
                    'date_to' => $value['date_to'] ?? null,
                    'consumed_hours' => $value['consumed_hours'] ?? null,
                    'sponsored_by' => $value['sponsored_by'] ?? null,
                ]);
            } 
        }
    }

    public function employee_others(string $employee_no, array $data) {
        $model = EmployeeOtherWorks::class;
 
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'organization' => $value['organization'] ?? null,
                    'address' => $value['address'] ?? null,
                    'date_from' => $value['date_from'] ?? null,
                    'date_to' => $value['date_to'] ?? null,
                    'consumed_hours' => $value['consumed_hours'] ?? null,
                    'position' => $value['position'] ?? null,
                ]);
            } 
        }
    }

    public function employee_skills(string $employee_no, array $data) {
        $model = EmployeeSkillsHobbies::class;
        
        if(!empty($data)) {
            foreach($data as $value) {
                $model::insert([
                    'employee_no' => $employee_no,
                    'name' => $value['name'] ?? null,
                    'recognition' => $value['recognition'] ?? null,
                    'organization' => $value['organization'] ?? null,
                ]);
            } 
        }
    }

    public function render()
    {
        return view('livewire.admin.hris.manual');
    }
    
}
