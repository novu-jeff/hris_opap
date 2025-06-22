<?php

namespace App\Livewire\Admin\Hris;

use App\Helper\Generate;
use App\Mail\SendEmployeeAccount;
use App\Models\EmployeeAccount;
use App\Models\EmployeeChildren;
use App\Models\EmployeeCivilService;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmploymentHistory;
use App\Models\EmployeeInformation;
use App\Models\EmployeeOtherWorks;
use App\Models\EmployeeParents;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeSkillsHobbies;
use App\Models\EmployeeTrainings;
use App\Models\EmployementTypes;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\Positions;
use App\Models\Sections;
use App\Models\ShiftSchedule;
use App\Models\Tranche;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Manual extends Component
{

    public $selected_id;
    public array $records;
    public object $positions;
    public object $sections;
    public object $employmentTypes;
    public object $shiftSchedule;
    public object $employeeSchedule;
    public array $countries;
    
    public $activeTab = 'details';
    public $activeAccordion = 'personal';
    public bool $isDualCitizenship = false;
    public bool $isMarried = false;
    public bool $hasBirthCert = false;
    public bool $hasMarriageCert = false;

    public function mount() {
        $this->loadRecords();
        $this->loadCountries();
    }

    public function loadRecords() {
        $this->sections = Sections::all();
        $this->positions = Positions::all();
        $this->employmentTypes = EmployementTypes::all();

        $this->shiftSchedule = ShiftSchedule::all();
        $this->employeeSchedule = EmployeeSchedule::all();

        $this->records['employee_information'] = [
            'employee_no' => '',
            'section_id' => '',
            'position_id' => '',
            'date_hired' => '',
            'biometrics_id' => '',
            'date_resignation' => '',
            'type' => '',
            'status' => '',
            'salary_method' => '',
            'monthly_rate' => '',
            'payroll_account_number' => '',
            'job_completion' => '',
            'step_id' => '',
        ];

    }

    public function loadCountries() {

        if (Cache::has('countries')) {
            return $this->countries = Cache::get('countries');
        }

        try {
            $client = new Client();
            $response = $client->get('https://restcountries.com/v3.1/all?fields=name');
            $countries = json_decode($response->getBody(), true);

            // Validate response structure
            if (!is_array($countries)) {
                throw new \Exception('Invalid API response');
            }
            usort($countries, fn($a, $b) => strcmp($a['name']['common'], $b['name']['common']));
            Cache::put('countries', $countries, now()->addHours(24));
            $this->countries = $countries;
            return $this->countries;
        } catch (\Exception $e) {
            return $this->countries = [];
        }
    }

    public function handleSalary() {
        
        $eligible = $this->records['employee_information']['type'] ?? '';
        $position_id = $this->records['employee_information']['position_id'] ?? '';
        $step_id = $this->records['employee_information']['step_id'] ?? '';

        if($eligible != 3) {
            
            if(!empty($eligible)) {
                $this->positions = Positions::where('type', $eligible)->get();
            }
    
            if (!empty($eligible) && !empty($position_id) && !empty($step_id)) {
    
                $salaryGrade = Positions::where('id', $position_id)
                    ->value('salary_grade') ?? '';
    
                $stepColumn = "step_" . ($step_id ?? '');
    
                $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn, $eligible) {
                        $query->where('salary_grade', $salaryGrade)
                            ->select('id', 'tranche_id', 'salary_grade', $stepColumn);
                    }])
                    ->where('eligible', $eligible)
                    ->first();
                
                // Ensure that $activeTranche is not null before accessing its items
                $salary = ($activeTranche && $activeTranche->items->isNotEmpty()) 
                    ? $activeTranche->items->first()->$stepColumn 
                    : 0;
            
    
                if ($activeTranche) {
                    $this->records['employee_information']['monthly_rate'] = $salary;
                }
            } else {
                $this->records['employee_information']['monthly_rate'] = 0;
            }
        } else {
            $this->records['employee_information']['monthly_rate'] = 0;
        }
    }

    private $tabAccordionMappings = [
        'employee_personal' => [
            'tab' => 'details',
            'accordions' => [
                'personal' => ['firstname', 'lastname', 'middlename', 'suffix', 'birthday', 'civil_status', 'sex', 'citizenship', 'citizenship_type', 'birth_certificate', 'marriage_certificate'],
                'address' => ['present_address', 'present_province', 'present_city', 'permanent_address', 'permanent_province', 'permanent_city'],
                'contact' => ['mobile_number', 'tel_no', 'email'],
                'appearance' => ['height', 'weight', 'blood_type'],
                'identification' => ['gsis_no', 'pagibig_no', 'philhealth_no', 'sss_no', 'tin_no']
            ]
        ],
        'employee_children' => [
            'tab' => 'family',
            'accordions' => [
                'parents' => ['spouse_surname', 'spouse_firstname', 'spouse_middlename', 'spouse_suffix', 'spouse_occupation', 'spouse_business_name_employer', 'spouse_business_address', 'spouse_contact_no', 'father_surname', 'father_firstname', 'father_middlename', 'father_suffix', 'mother_surname', 'mother_firstname', 'mother_middlename'],
                'children' => ['firstname', 'lastname', 'middlename', 'birthdate']
            ]
        ],
        'employee_education' => ['tab' => 'education'],
        'employee_employment_history' => ['tab' => 'history'],
        'employee_civil_service' => ['tab' => 'civil_service'],
        'employee_trainings' => ['tab' => 'trainings'],
        'employee_others' => ['tab' => 'others'],
        'employee_skills' => ['tab' => 'skills'],
        'employee_account' => ['tab' => 'account'],
    ];

    private $defaultFields = [
        'employee_education' => [
            'level' => '',
            'school_name' => '',
            'course' => '',
            'from_year' => '',
            'to_year' => '',
            'documents' => '',
        ],
        'employee_employment_history' => [
            'position' => '',
            'department' => '',
            'company_name' => '',
            'monthly_salary' => '',
            'employment_status' => '',
            'isGovernment' => '',
            'from_year' => '',
            'to_year' => '',
            'documents' => '',
        ],
        'employee_children' => [
            'firstname' => '',
            'middlename' => '',
            'lastname' => '',
            'birthdate' => '',
            'documents' => '',
        ],
        'employee_civil_service' => [
            'documents' => ''
        ],
        'employee_trainings' => [
            'documents' => ''
        ],
        'employee_others' => [
            'documents' => ''
        ],
        'employee_skills' => [
            'documents' => ''
        ],
    ];
    
    public function setActiveTab($tab) {
        $this->activeTab = $tab;
    }

    public function setActiveAccordion($accordion) {
        $this->activeAccordion = $accordion;
    }

    public function select_change(string $property) {
        $this->setActiveAccordion('personal');
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
            } else {
                $this->records['employee_information']['branch'] = '';
                $this->records['employee_information']['department'] = '';
            }
        }
    }

    public function addRecord($tab, $type, $accordion = null) {
        $this->activeAccordion = $accordion;
        if (isset($this->defaultFields[$type])) {
            $this->records[$type][] = $this->defaultFields[$type];
        }
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
            $type = $parts[1] ?? null;
            $field = end($parts);

            if ($type && isset($this->tabAccordionMappings[$type])) {
                $mapping = $this->tabAccordionMappings[$type];
                $this->activeTab = $mapping['tab'];

                if (isset($mapping['accordions'])) {
                    $this->activeAccordion = $this->findAccordionKey($field, $mapping['accordions']);
                }

                return; // Break after finding the first match
            }
        }
    }

    private function findAccordionKey(string $key, array $accordions) {
        foreach ($accordions as $accordionKey => $fields) {
            if (in_array($key, $fields)) {
                return $accordionKey;
            }
        }
        return null;
    }

    protected function rules(? int $id = null) {
        return [
            'records.employee_information.employee_no' => [
                'required',
                Rule::unique('employee_information', 'employee_no')->ignore($id, 'employee_no')
            ],
            'records.employee_information.biometrics_id' => [
                Rule::unique('employee_information', 'bsd_no')->ignore($id, 'employee_no')
            ],
            'records.employee_information.status' => 'required|in:active,inactive',
            'records.employee_information.date_hired' => 'required|date',
            'records.employee_information.position_id' => 'required_if:records.employee_information.type,1,2|exists:positions,id',
            'records.employee_information.job_completion' => 'nullable|date|required_if:records.employee_information.position_id,3',

            'records.employee_information.step_id' => 'required|in:1,2,3,4,5,6,7,8',
            'records.employee_information.section_id' => 'required|exists:sections,id',
            'records.employee_information.monthly_rate' => 'required|numeric|gt:1000',
            'records.employee_information.salary_method' => 'required|in:cash,bank transfer,paycheck,e-wallet',
            'records.employee_information.type' => 'required|exists:employment_types,id',


            'records.employee_personal.firstname' => 'required|string|max:255',
            'records.employee_personal.lastname' => 'required|string|max:255',
            'records.employee_personal.suffix' => 'nullable|in:jr,sr,I,II,III,IV,V',
            'records.employee_personal.civil_status' => 'nullable|in:single,married,divorced,seperated,widowed,anulled',
            'records.employee_personal.sex' => 'required|in:male,female',
            'records.employee_personal.citizenship_type' => 'nullable|required_with:records.employee_personal.citizenship',
            'records.employee_personal.country' => 'required_if:records.employee_personal.citizenship,dual_citizenship',

            'records.employee_personal.birth_certificate' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_personal.marriage_certificate' => 'nullable|mimes:jpg,png,jpeg,pdf',

            'records.employee_personal.mobile_number' => 'nullable|regex:/^09\d{9}$/',
            'records.employee_personal.email' => [
                'required',
                Rule::unique('employee_account', 'email')->ignore($id, 'employee_no')
            ],

            'records.employee_children.*.firstname' => 'required|string|max:255',
            'records.employee_children.*.middlename' => 'nullable|string|max:255',
            'records.employee_children.*.lastname' => 'required|string|max:255',
            'records.employee_children.*.birthdate' => 'required|date', 
            'records.employee_children.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_children.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },

            'records.employee_education.*.level' => 'required|string',
            'records.employee_education.*.school_name' => 'required|string|max:255',
            'records.employee_education.*.course' => 'required|string|max:255',
            'records.employee_education.*.from_year' => 'required|date',
            'records.employee_education.*.to_year' => 'required|date',
            'records.employee_education.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_education.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
            'records.employee_employment_history.*.position' => 'required|string|max:255',
            'records.employee_employment_history.*.department' => 'required|string|max:255',
            'records.employee_employment_history.*.company_name' => 'required|string|max:255',
            'records.employee_employment_history.*.employment_status' => 'required|string',
            'records.employee_employment_history.*.isGovernment' => 'required|string',
            'records.employee_employment_history.*.from_year' => 'required|date',
            'records.employee_employment_history.*.to_year' => 'required|date|after_or_equal:records.employee_employment_history.*.from_year',
            'records.employee_employment_history.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_employment_history.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
            'records.employee_civil_service.*.certification' => 'required|string|max:255',
            'records.employee_civil_service.*.rating' => 'required|string|max:255',
            'records.employee_civil_service.*.date_exam' => 'required|string|max:255',
            'records.employee_civil_service.*.place_exam' => 'required|string|max:255',
            'records.employee_civil_service.*.license_no' => 'required|string|max:255',
            'records.employee_civil_service.*.date_validity' => 'required|date',
            'records.employee_civil_service.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_civil_service.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
            'records.employee_trainings.*.type' => 'required|string|max:255',
            'records.employee_trainings.*.name' => 'required|string|max:255',
            'records.employee_trainings.*.date_from' => 'required|string|max:255',
            'records.employee_trainings.*.date_to' => 'required|string|max:255',
            'records.employee_trainings.*.consumed_hours' => 'required|integer',
            'records.employee_trainings.*.sponsored_by' => 'required|string|max:255',
            'records.employee_trainings.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_trainings.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
            'records.employee_others.*.organization' => 'required|string|max:255',
            'records.employee_others.*.address' => 'required|string|max:255',
            'records.employee_others.*.date_from' => 'required|string|max:255',
            'records.employee_others.*.date_to' => 'required|string|max:255',
            'records.employee_others.*.consumed_hours' => 'required|integer',
            'records.employee_others.*.position' => 'required|string|max:255',
            'records.employee_others.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_others.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
            'records.employee_skills.*.name' => 'required|string|max:255',
            'records.employee_skills.*.recognition' => 'required|string|max:255',
            'records.employee_skills.*.organization' => 'required|string|max:255',
            'records.employee_skills.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.employee_skills.*.documents' => function ($attribute, $value, $fail) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                $files = is_array($value) ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail("The document must be a JPEG, PNG, or PDF file.");
                        }
                    } 
                }
            },
            'records.employee_account.notify_user' => 'boolean',
            'records.employee_account.password' => 'required|min:8|same:records.employee_account.confirm_password',
            'records.employee_account.confirm_password' => 'required_with:records.employee_account.password|min:8'
        ];
    }

    public function messages() {
        return [
            'records.employee_information.employee_no.required' => 'The employee no is required.',
            'records.employee_information.employee_no.unique' => 'The employee no is already taken.',
            'records.employee_information.biometrics_id.required' => 'The biometrics ID is required.',
            'records.employee_information.biometrics_id.unique' => 'The biometrics ID is already taken.',
            'records.employee_information.type.in' => 'The selected employment type does not exists.',
            'records.employee_information.status.required' => 'The account status is required.',
            'records.employee_information.status.in' => 'The account status must be either active or inactive.',
            'records.employee_information.date_hired.required' => 'The date hired is required',
            'records.employee_information.date_hired.date' => 'The date hired must be valid date',
            'records.employee_information.monthly_rate.required' => 'The monthly rate is required',
            'records.employee_information.monthly_rate.numeric' => 'The monthly rate must be numbers',
            'records.employee_information.monthly_rate.gt' => 'The monthly rate must be greather than 1000',
            'records.employee_information.section_id.required' => 'The section is required.',
            'records.employee_information.section_id.exists' => 'The selected section does not exist.',
            'records.employee_information.position_id.required' => 'The position is required.',
            'records.employee_information.position_id.exists' => 'The selected position does not exist.',
            'records.employee_information.step_id.required' => 'The tranche step is required.',
            'records.employee_information.step_id.in' => 'The tranche step is invalid.',
            'records.employee_information.salary_method.required' => 'The salary method is required',
            'records.employee_information.salary_method.in' => 'The salary method must be one of the following: cash, bank transfer, paycheck, or e-wallet.',
            'records.employee_information.type.required' => 'The employment type is required',
            'records.employee_information.type.exists' => 'The selected employment type does not exists.',

            'records.employee_personal.firstname.required' => 'The first name is required.',
            'records.employee_personal.lastname.required' => 'The last name is required.',
            'records.employee_personal.suffix.in' => 'The suffix must be one of the following: jr, sr, I, II, III, IV, or V.',
            'records.employee_personal.civil_status.in' => 'The civil status must be one of the following: single, married, divorced, separated, widowed, or annulled.',
            'records.employee_personal.sex.required' => 'The sex field is required.',
            'records.employee_personal.sex.in' => 'The sex must be either male or female.',
            'records.employee_personal.citizenship_type.required_with' => 'The citizenship type is required when citizenship is provided.',
            'records.employee_personal.country.required_if' => 'The country is required when citizenship is dual citizenship.',
            'records.employee_personal.birth_certificate.mimes' => 'The birth certificate must be an image or pdf',
            'records.employee_personal.marriage_certificate.mimes' => 'The birth certificate must be an image or pdf',

            'records.employee_personal.mobile_number.regex' => 'The mobile number format is invalid. It should start with 09 and be followed by 9 digits.',
            'records.employee_personal.email.email' => 'The email must be a valid email address.',
            'records.employee_personal.email.required' => 'The email is required.',
            'records.employee_personal.email.unique' => 'The email is already taken.',
            
            'records.employee_education.*.level.required' => 'The education level is required.',
            'records.employee_education.*.school_name.required' => 'The school name is required.',
            'records.employee_education.*.course.required' => 'The course name is required.',
            'records.employee_education.*.from_year.required' => 'The start year is required.',
            'records.employee_education.*.to_year.required' => 'The end year is required.',
            'records.employee_education.*.to_year.after_or_equal' => 'The end year must be the same or after the start year.',
            'records.employee_education.*.documents.mimes' => 'The document must be an image or pdf',

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
            'records.employee_employment_history.*.documents.mimes' => 'The document must be an image or pdf',

            'records.employee_civil_service.*.certification.required' => 'The certification field is required for each civil service record.',
            'records.employee_civil_service.*.rating.required' => 'The rating field is required for each civil service record.',
            'records.employee_civil_service.*.date_exam.required' => 'The date of the exam is required for each civil service record.',
            'records.employee_civil_service.*.place_exam.required' => 'The place of the exam is required for each civil service record.',
            'records.employee_civil_service.*.license_no.required' => 'The license number is required for each civil service record.',
            'records.employee_civil_service.*.date_validity.required' => 'The date of validity is required for each civil service record.',
            'records.employee_civil_service.*.date_validity.date' => 'The date validity must be a valid date for each civil service record.',
            'records.employee_civil_service.*.documents.mimes' => 'The document must be an image or pdf',

            'records.employee_trainings.*.type.required' => 'The training type is required for each training record.',
            'records.employee_trainings.*.name.required' => 'The training name is required for each training record.',
            'records.employee_trainings.*.date_from.required' => 'The start date is required for each training record.',
            'records.employee_trainings.*.date_to.required' => 'The end date is required for each training record.',
            'records.employee_trainings.*.consumed_hours.required' => 'The consumed hours field is required for each training record.',
            'records.employee_trainings.*.consumed_hours.integer' => 'The consumed hours must be a valid integer for each training record.',
            'records.employee_trainings.*.sponsored_by.required' => 'The sponsored by field is required for each training record.',
            'records.employee_trainings.*.documents.mimes' => 'The document must be an image or pdf',

            'records.employee_others.*.organization.required' => 'The organization field is required for each other record.',
            'records.employee_others.*.address.required' => 'The address field is required for each other record.',
            'records.employee_others.*.date_from.required' => 'The start date is required for each other record.',
            'records.employee_others.*.date_to.required' => 'The end date is required for each other record.',
            'records.employee_others.*.consumed_hours.required' => 'The consumed hours field is required for each other record.',
            'records.employee_others.*.consumed_hours.integer' => 'The consumed hours must be a valid integer for each other record.',
            'records.employee_others.*.position.required' => 'The position field is required for each other record.',
            'records.employee_others.*.documents.mimes' => 'The document must be an image or pdf',

            'records.employee_skills.*.name.required' => 'The skill name field is required for each skill record.',
            'records.employee_skills.*.recognition.required' => 'The recognition field is required for each skill record.',
            'records.employee_skills.*.organization.required' => 'The organization field is required for each skill record.',  
            'records.employee_skills.*.documents.mimes' => 'The document must be an image or pdf',

            'records.employee_account.notify_user.boolean' => 'The Notify User field must be true or false.',        
            'records.employee_account.password.required' => 'The password is required.',
            'records.employee_account.password.max' => 'The password must be at least 8 characters.',
            'records.employee_account.password.same' => 'The password and confirmation password must match.',
            'records.employee_account.confirm_password.required_with' => 'The confirm password field is required.',
            'records.employee_account.confirm_password.max' => 'The confirm password must be at least 8 characters.',
        ];
    }

    public function save() {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        try {
            $this->validate($this->rules());
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->keys();
            $this->setErrorActiveTabAccordions($errors);
            $this->dispatch('scrollToError', $errors);
            throw $e; 
        }
                
        DB::beginTransaction();

        try {
            

            $this->records['employee_personal']['email'] = $this->records['employee_personal']['email'];
            $this->records['employee_personal']['password'] = $this->records['employee_account']['password'];


            $record = $this->employee_information($this->records['employee_information']);
            $this->employee_personal($record->employee_no, $this->records['employee_personal'] ?? []);
            $this->employee_account($record->employee_no, $this->records['employee_personal'] ?? []);
            $this->employee_parents($record->employee_no, $this->records['employee_parents'] ?? []);
            $this->employee_children($record->employee_no, $this->records['employee_children'] ?? []);
            $this->employee_education($record->employee_no, $this->records['employee_education'] ?? []);
            $this->employee_employment_history($record->employee_no, $this->records['employee_employment_history'] ?? []);
            $this->employee_civil_service($record->employee_no, $this->records['employee_civil_service'] ?? []);
            $this->employee_trainings($record->employee_no, $this->records['employee_trainings'] ?? []);
            $this->employee_others($record->employee_no, $this->records['employee_others'] ?? []);
            $this->employee_skills($record->employee_no, $this->records['employee_skills'] ?? []);
            $this->employee_leave($record->employee_no, $this->records);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee ' . strtoupper($record->employee_no) . ' records successfully.',
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
            'bsd_no' => $data['biometrics_id'] ?? null,
            'date_resignation' => $data['date_resignation'] ?? null,
            'employment_type_id' => $data['type'] ?? null,
            'status' => $data['status'] ?? null,
            'salary_method' => $data['salary_method'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? null,
            'payroll_account_number' => $data['payroll_account_number'] ?? null,
        ]);
    }

    public function employee_account(string $employee_no, array $data) {

        $generate = new Generate;

        $applicant_id = $data['applicant_id'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $email_id = $generate->email($employee_no, $firstname, $lastname);

        $user = EmployeeAccount::create([
            'employee_no' => $employee_no,
            'applicant_id' => $applicant_id,
            'email_id' => $email_id,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $user->assignRole('employee');

        $record = EmployeeInformation::with('personal', 'account')->where('employee_no', $employee_no)->first();


        if (!$record || empty($record->account->email)) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Unable to notify this employee, their email address is invalid or empty. Please update it first!'
            ]);
        }

        $data = [
            'is_newly_hired' => false,
            'employee_no' => $record->employee_no,
            'email' => $email_id,
            'fullname' => $record->personal->firstname . ' ' . $record->personal->lastname,
            'password' => $password
        ];

        Mail::to($email)->send(new SendEmployeeAccount($data));

        return;
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
                    'date_validity' => $value['date_validity'] ?? null,
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

    public function employee_leave(string $employee_no, array $data) {

        $leaveDefaultCredits = LeaveType::all();

        $model = LeaveCredits::class;

        $product = config('app.product');

        // if ($product == 'government') {
        //     if ($data['employee_information']['type'] == 1) {
        //         foreach ($leaveDefaultCredits as $leave) {

        //             $credits = 0;
                    
        //             $existingLeave = $model::where('employee_no', $employee_no)
        //                 ->where('leave_type_id', $leave->id)
        //                 ->first();
                        
        //             if($existingLeave && $existingLeave->credits != 0) {
        //                 if ($leave->code == 'ML' && $data['employee_personal']['sex'] == 'female') {
        //                     $credits = $existingLeave->credits;
        //                 }

        //                 elseif ($leave->code == 'PL' && $data['employee_personal']['sex'] == 'male') {
        //                     $credits = $existingLeave->credits;
        //                 }

        //                 elseif ($leave->code == 'SOLO' || $leave->code == 'SPL') {
        //                     $credits = 0;
        //                 }

        //                 elseif ($leave->code !== 'PL' && $leave->code !== 'ML') {
        //                     $credits = $existingLeave->credits;
        //                 }
        //             } else {
        //                 if ($leave->code == 'ML' && $data['employee_personal']['sex'] == 'female') {
        //                     $credits = $leave->credits;
        //                 }

        //                 elseif ($leave->code == 'PL' && $data['employee_personal']['sex'] == 'male') {
        //                     $credits = $leave->credits;
        //                 }

        //                 elseif ($leave->code == 'SOLO' || $leave->code == 'SPL') {
        //                     $credits = 0;
        //                 }

        //                 elseif ($leave->code !== 'PL' && $leave->code !== 'ML') {
        //                     $credits = $leave->credits;
        //                 }
        //             }

                    
                
        //             // Update or create the record with the appropriate credits
        //             $model::updateOrCreate(
        //                 [
        //                     'employee_no' => $employee_no,
        //                     'leave_type_id' => $leave->id,
        //                 ],
        //                 [
        //                     'credits' => $credits,
        //                 ]
        //             );
        //         }
                
                
        //     } else {
        //         foreach ($leaveDefaultCredits as $leave) {
        //             $model::updateOrCreate(
        //                 [
        //                     'employee_no' => $employee_no,
        //                     'leave_type_id' => $leave->id,
        //                 ],
        //                 [
        //                     'credits' => 0,
        //                 ]
        //             );
        //         }
        //     }
        // }

    }

    public function render()
    {
        return view('livewire.admin.hris.manual');
    }
    
}
