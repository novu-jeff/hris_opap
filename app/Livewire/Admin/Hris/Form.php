<?php

namespace App\Livewire\Admin\Hris;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Mail\SendEmployeeAccount;
use App\Models\CompanyInformation;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSchedule;
use App\Models\EmployementTypes;
use App\Models\JobCategory;
use App\Models\Positions;
use App\Models\Sections;
use App\Models\ShiftSchedule;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Form extends Component
{

    public string $employee_no;
    public object $sections;
    public object $positions;
    public object $employmentTypes;
    public object $shiftSchedule;
    public object $employeeSchedule;
    public array $records;
    public $countries;

    public $activeTab = 'details';
    public $activeAccordion = 'personal';
    public bool $isDualCitizenship = false;

    public function mount() {
        $this->loadRecords();
        $this->loadCountries();
    }

    public function loadRecords() {
        // Redirect if employee number is not provided
        if (empty($this->employee_no)) {
            return redirect()->route('hris.index');
        }

        // Load dropdown data
        $this->sections = Sections::all();
        $this->positions = Positions::all();
        $this->employmentTypes = EmployementTypes::all();

        $this->shiftSchedule = ShiftSchedule::all();
        $this->employeeSchedule = EmployeeSchedule::all();


        // Fetch related earnings and deductions
        $otherServices = new OtherServices();
        $earnings = $otherServices->earnings($this->employee_no) ?? [];
        $deductions = $otherServices->deductions($this->employee_no) ?? [];
        $leaveCredits = $otherServices->leaves($this->employee_no) ?? [];

        // Fetch employee data with relations
        $data = EmployeeInformation::with([
            'department',
            'personal.gsis_item.gsis',
            'account',
            'education',
            'parents',
            'children',
            'employment_history',
            'civil_service',
            'trainings',
            'others',
            'skills'
        ])->where('employee_no', $this->employee_no)->first();

        // Reset records if no data is found
        if (!$data) {
            $this->records = [];
            return;
        }

        // Populate employee records
        $this->records = [
            'employee_information' => $this->formatEmployeeInformation($data),
            'employee_account' => $this->formatEmployeeAccount($data),
            'employee_personal' => $this->formatEmployeePersonal($data),
            'employee_education' => $data->education->toArray(),
            'employee_parents' => $this->formatEmployeeParents($data),
            'employee_children' => $data->children->toArray(),
            'employee_employment_history' => $data->employment_history->toArray(),
            'employee_civil_service' => $data->civil_service->toArray(),
            'employee_trainings' => $data->trainings->toArray(),
            'employee_others' => $data->others->toArray(),
            'employee_skills' => $data->skills->toArray(),
            'employee_gsis' => $data->personal?->gsis_item?->toArray() ?? [],
            'other_earnings' => $earnings,
            'other_deductions' => $deductions,
            'leaveCredits' => $leaveCredits
        ];

        if ($data->section_id) {
            $this->select_change('section');
        }

        if($data->personal->citizenship == 'dual_citizenship') {
            $this->select_change('citizenship');
        }
    }


    /**
     * Format employee information data
     */
    protected function formatEmployeeInformation($data) {
        return [
            'id' => $data->id,
            'employee_id' => format_id($data->id, 6),
            'employee_no' => $data->employee_no,
            'biometrics_id' => $data->bsd_no,
            'shift_schedule' => $data->shift_id,
            'employee_schedule' => $data->schedule_id,
            'section_id' => $data->section_id,
            'position_id' => $data->position_id,
            'date_hired' => $data->date_hired,
            'service_duration' => relative_time_duration($data->date_hired),
            'date_resignation' => $data->date_resignation,
            'type' => $data->employment_type_id,
            'status' => $data->status,
            'salary_method' => $data->salary_method,
            'monthly_rate' => $data->monthly_rate,
            'payroll_account_number' => $data->payroll_account_number,
        ];
    }

    /**
     * Format employee account data
     */
    protected function formatEmployeeAccount($data) {
        return [
            'email' => $data->account->email ?? null,
        ];
    }

    /**
     * Format employee personal data
     */
    protected function formatEmployeePersonal($data) {
        $personal = $data->personal;
        return [
            'profile' => $personal->profile ?? null,
            'firstname' => $personal->firstname ?? null,
            'middlename' => $personal->middlename ?? null,
            'lastname' => $personal->lastname ?? null,
            'suffix' => $personal->suffix ?? null,
            'birthday' => $personal->birthday ?? null,
            'civil_status' => $personal->civil_status ?? null,
            'sex' => $personal->sex ?? null,
            'citizenship' => $personal->citizenship ?? null,
            'citizenship_type' => $personal->citizenship_type ?? null,
            'country' => $personal->country ?? null,
            'present_address' => $personal->present_address ?? null,
            'present_province' => $personal->present_province ?? null,
            'present_city' => $personal->present_city ?? null,
            'permanent_address' => $personal->permanent_address ?? null,
            'permanent_province' => $personal->permanent_province ?? null,
            'permanent_city' => $personal->permanent_city ?? null,
            'mobile_number' => $personal->mobile_number ?? null,
            'tel_no' => $personal->tel_no ?? null,
            'email' => $personal->email ?? null,
            'height' => $personal->height ?? null,
            'weight' => $personal->weight ?? null,
            'blood_type' => $personal->blood_type ?? null,
            'gsis_no' => $personal->gsis_no ?? null,
            'pagibig_no' => $personal->pagibig_no ?? null,
            'philhealth_no' => $personal->philhealth_no ?? null,
            'sss_no' => $personal->sss_no ?? null,
            'tin_no' => $personal->tin_no ?? null,
        ];
    }

    /**
     * Format employee parents data
     */
    protected function formatEmployeeParents($data) {
        $parents = $data->parents;
        return [
            'spouse_surname' => $parents->spouse_surname ?? null,
            'spouse_firstname' => $parents->spouse_firstname ?? null,
            'spouse_middlename' => $parents->spouse_middlename ?? null,
            'spouse_suffix' => $parents->spouse_suffix ?? null,
            'spouse_occupation' => $parents->spouse_occupation ?? null,
            'spouse_business_name_employer' => $parents->spouse_business_name_employer ?? null,
            'spouse_business_address' => $parents->spouse_business_address ?? null,
            'spouse_contact_no' => $parents->spouse_contact_no ?? null,
            'father_surname' => $parents->father_surname ?? null,
            'father_firstname' => $parents->father_firstname ?? null,
            'father_middlename' => $parents->father_middlename ?? null,
            'father_suffix' => $parents->suffix ?? null,
            'mother_surname' => $parents->mother_surname ?? null,
            'mother_firstname' => $parents->mother_firstname ?? null,
            'mother_middlename' => $parents->mother_middlename ?? null,
        ];
    }

    public function loadCountries() {
        // Check cache first (e.g., using Laravel Cache)
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

            // Sort countries by common name
            usort($countries, fn($a, $b) => strcmp($a['name']['common'], $b['name']['common']));

            // Cache the result for 24 hours
            Cache::put('countries', $countries, now()->addHours(24));

            $this->countries = $countries;
            return $this->countries;
        } catch (\Exception $e) {
            // Log the error
            logger()->error('Failed to load countries: ' . $e->getMessage());

            // Provide a default empty array if an error occurs
            return $this->countries = [];
        }
    }

    private $tabAccordionMappings = [
        'employee_personal' => [
            'tab' => 'details',
            'accordions' => [
                'personal' => ['firstname', 'lastname', 'middlename', 'suffix', 'birthday', 'civil_status', 'sex', 'citizenship', 'citizenship_type'],
                'address' => ['present_address', 'present_province', 'present_city', 'permanent_address', 'permanent_province', 'permanent_city'],
                'contact' => ['mobile_number', 'tel_no', 'company_email'],
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

    public function setActiveTab($tab) {
        $this->activeTab = $tab;
    }

    public function setActiveAccordion($accordion) {
        $this->activeAccordion = $accordion;
    }

    public function select_change(string $property) {
        $this->setActiveAccordion('personal');
        if($property == 'citizenship') {
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

    protected function rules(string $employee_no = null) {
        return [
            'records.employee_information.employee_no' => [
                'required',
                Rule::unique('employee_information', 'employee_no')->ignore($employee_no, 'employee_no')
            ],
            'records.employee_information.biometrics_id' => [
                'required',
                Rule::unique('employee_information', 'bsd_no')->ignore($employee_no, 'employee_no')
            ],
            'records.employee_information.status' => 'required|in:active,inactive',
            'records.employee_information.date_hired' => 'required|date',
            'records.employee_information.position_id' => 'required|exists:positions,id',
            'records.employee_information.section_id' => 'nullable|exists:sections,id',
            'records.employee_information.monthly_rate' => 'required|numeric|gt:1000',
            'records.employee_information.salary_method' => 'nullable|in:cash,bank transfer,paycheck,e-wallet',
            'records.employee_information.type' => 'required|exists:employment_types,id',

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

            'records.employee_account.notify_user' => 'boolean',
            'records.employee_account.password' => 'required_with:records.employee_account.notify_user,true|min:8|same:records.employee_account.confirm_password',
            'records.employee_account.confirm_password' => 'required_with:records.employee_account.password|min:8'

        ];
    }

    protected function messages() {
        return [
            'records.employee_information.employee_no.required' => 'The employee no is required.',
            'records.employee_information.employee_no.unique' => 'The employee no is already taken.',
            'records.employee_information.biometrics_id.required' => 'The biometrics ID is required.',
            'records.employee_information.biometrics_id.unique' => 'The biometrics ID is already taken.',
            'records.employee_information.type.in' => 'The selected employment type does not exists.',
            'records.employee_information.status.required' => 'The account status is required.',
            'records.employee_information.status.in' => 'The status must be either active or inactive.',
            'records.employee_information.date_hired.required' => 'The date hired is required',
            'records.employee_information.date_hired.date' => 'The date hired must be valid date',
            'records.employee_information.monthly_rate.required' => 'The monthly rate is required',
            'records.employee_information.monthly_rate.numeric' => 'The monthly rate must be numbers',
            'records.employee_information.monthly_rate.gt' => 'The monthly rate must be greather than 1000',
            'records.employee_information.section_id.required' => 'The section is required.',
            'records.employee_information.position_id.required' => 'The position is required.',
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
            
            'records.employee_account.notify_user.boolean' => 'The Notify User field must be true or false.',
            'records.employee_account.password.required_with' => 'The Password field is required when Notify User is enabled.',
            'records.employee_account.password.min' => 'The Password must be at least 8 characters.',
            'records.employee_account.password.same' => 'The Password and Confirm Password must match.',
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

        $id = $this->employee_no;

        // Check if the employee exists
        $record = EmployeeInformation::where('employee_no', $id)->first();
        if (!$record) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: You\'re saving a non-existent employee!'
            ]);
        }

        // Validate input data
        try {
            $this->validate($this->rules($id));
        } catch (ValidationException $e) {
            $this->setErrorActiveTabAccordions($e->validator->errors()->keys());
            throw $e;
        }

        DB::beginTransaction();

        try {
            // Save the employee data
            $process = new HRISProcessingService;
            $process->save(false, $id, $id, $this->records);

            // Notify user if necessary
            $account = $this->records['employee_account'] ?? [];
            if (!empty($account['notify_user']) && !empty($account['password'])) {
                $record = EmployeeInformation::with('personal', 'account')->where('employee_no', $id)->first();

                if (!$record || empty($record->personal->email)) {
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
                    'email' => $record->account->email,
                    'fullname' => $record->personal->firstname . ' ' . $record->personal->lastname,
                    'password' => $account['password']
                ];

                Mail::to($record->personal->email)->send(new SendEmployeeAccount($data));
            }

            DB::commit();

            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee ' . strtoupper($id) . ' records saved successfully.',
                'redirect' => route('hris.show', ['employee_no' => $id])
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

    public function render() {
        return view('livewire.admin.hris.form');
    }
}
