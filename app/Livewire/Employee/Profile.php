<?php

namespace App\Livewire\Employee;

use App\Livewire\Admin\Hris\Manual;
use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\EmployeeUpdateChildren;
use App\Models\EmployeeUpdateCivilService;
use App\Models\EmployeeUpdateEducation;
use App\Models\EmployeeUpdateEmploymentHistory;
use App\Models\EmployeeUpdateOtherWorks;
use App\Models\EmployeeUpdateParents;
use App\Models\EmployeeUpdatePersonal;
use App\Models\EmployeeUpdateSkillsHobbies;
use App\Models\EmployeeUpdateTrainings;
use App\Notifications\Notifications;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Profile extends Component
{

    use WithFileUploads;

    public $employee_no;
    public $employee_id;
    public $employees;
    public array $records;
    public array $countries;
    public bool $isFromUpdate = false;

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

    public function loadRecords()
    {
        $this->employee_no = Auth::user()->employee_no;
        $this->employee_id = Auth::user()->id;
    
        // Load updated data
        $updating = EmployeeUpdatePersonal::with([
            'education', 'parents', 'children', 'employment_history', 
            'civil_service', 'trainings', 'others', 'skills', 
        ])->where('employee_no', $this->employee_no)->first();
    
        // Load stored data
        $stored = EmployeeInformation::with([
            'personal', 'education', 'parents', 'children', 
            'employment_history', 'civil_service', 'trainings', 
            'others', 'skills', 'account'
        ])->where('employee_no', $this->employee_no)->first();
    
        if (!$updating && !$stored) {
            $this->isFromUpdate = false;
            $this->records = [];
            return;
        }
    
        $this->isFromUpdate = (bool) $updating;
    
        // Define relationships to fallback from updated -> stored
        $fallbackRelations = [
            'children', 'employment_history', 'civil_service',
            'trainings', 'others', 'skills',
        ];
    
        // Final data source: combine updated and stored
        $data = $updating ?? $stored;

        $this->records = [
            'employee_personal' => $this->formatEmployeePersonal($data),
            'employee_education' => $data->education?->toArray() ?? [],
            'employee_parents' => $this->formatEmployeeParents($data),
        ];
    
        // Loop through each fallback relationship
        foreach ($fallbackRelations as $relation) {
            $this->records["employee_{$relation}"] =
                ($updating && $updating->$relation && $updating->$relation->isNotEmpty())
                    ? $updating->$relation->toArray()
                    : ($stored->$relation->toArray() ?? []);
        }
    
        // Handle citizenship logic
        $citizenship = $updating->citizenship ?? $stored->personal->citizenship ?? null;
        if ($citizenship === 'dual_citizenship') {
            $this->select_change('citizenship');
        }
    }
    
    protected function formatEmployeePersonal($data) {

        if($data->birth_certificate) {
            $this->hasBirthCert = true;
        } 

        if($data->marriage_certificate) {
            $this->hasMarriageCert = true;
        } 

        // Determine if it's an update or from the given data
        $personal = $this->isFromUpdate ? $data : $data->personal;
    
        // Extract the email from the 'account' array if it exists
        $email = $data->email ? $data->email  : $data->account['email'];
    
        // Define the fields for personal data
        $fields = [
            'profile', 'firstname', 'middlename', 'lastname', 'suffix', 'birthday',
            'civil_status', 'sex', 'citizenship', 'citizenship_type', 'country',
            'present_address', 'present_province', 'present_city', 'permanent_address',
            'permanent_province', 'permanent_city', 'mobile_number', 'tel_no', 'height',
            'weight', 'blood_type', 'gsis_no', 'pagibig_no', 'philhealth_no', 'sss_no',
            'tin_no', 'email' 
        ];
    
        // Generate the array with the personal fields and their values
        $formattedPersonal = array_combine(
            $fields, 
            array_map(fn($field) => $field === 'email' ? $email : ($personal[$field] ?? null), $fields)
        );
    
        return $formattedPersonal;
    }
    
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

            return $this->countries = [];
        }
    }

    private $tabAccordionMappings = [
        'employee_personal' => [
            'tab' => 'details',
            'accordions' => [
                'personal' => ['firstname', 'lastname', 'middlename', 'suffix', 'birthday', 'civil_status', 'sex', 'citizenship', 'citizenship_type', 'birth_certificate', 'marriage_certificate'],
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
                'children' => ['firstname', 'lastname', 'middlename', 'birthdate', 'documents']
            ]
        ],
        'employee_education' => ['tab' => 'education', 'documents'],
        'employee_employment_history' => ['tab' => 'history', 'documents'],
        'employee_civil_service' => ['tab' => 'civil_service', 'documents'],
        'employee_trainings' => ['tab' => 'trainings', 'documents'],
        'employee_others' => ['tab' => 'others', 'documents'],
        'employee_skills' => ['tab' => 'skills', 'documents'],
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
            if($this->records['employee_personal']['citizenship'] == 'dual_citizenship') {
                $this->isDualCitizenship = true;
            } else {
                $this->isDualCitizenship = false;
            }
        }

        if($property == 'civil_status') {
            if($this->records['employee_personal']['civil_status'] == 'married') {
                $this->isMarried = true;
            } else {
                $this->isMarried = false;
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

    protected function rules(?string $employee_no = null) {
        return [
         
            'records.employee_personal.firstname' => 'required|string|max:255',
            'records.employee_personal.lastname' => 'required|string|max:255',
            'records.employee_personal.suffix' => 'nullable|in:jr,sr,I,II,III,IV,V',
            'records.employee_personal.civil_status' => 'nullable|in:single,married,divorced,seperated,widowed,anulled',
            'records.employee_personal.sex' => 'nullable|in:male,female',
            'records.employee_personal.citizenship_type' => 'nullable|required_with:records.employee_personal.citizenship',
            'records.employee_personal.country' => 'required_if:records.employee_personal.citizenship,dual_citizenship',

            'records.employee_personal.mobile_number' => 'nullable|regex:/^09\d{9}$/',
            'records.employee_personal.email' => [
                'required',
                Rule::unique('employee_account', 'email')->ignore($employee_no, 'employee_no')
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
            'records.employee_employment_history.*.monthly_salary' => 'required|numeric|min:0',
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
        ];
    }

    protected function messages() {
        return [
           
            'records.employee_personal.firstname.required' => 'The first name is required.',
            'records.employee_personal.lastname.required' => 'The last name is required.',
            'records.employee_personal.suffix.in' => 'The suffix must be one of the following: jr, sr, I, II, III, IV, or V.',
            'records.employee_personal.civil_status.in' => 'The civil status must be one of the following: single, married, divorced, separated, widowed, or annulled.',
            'records.employee_personal.sex.in' => 'The sex must be either male or female.',
            'records.employee_personal.citizenship_type.required_with' => 'The citizenship type is required when citizenship is provided.',
            'records.employee_personal.country.required_if' => 'The country is required when citizenship is dual citizenship.',

            'records.employee_personal.mobile_number.regex' => 'The mobile number format is invalid. It should start with 09 and be followed by 9 digits.',
            'records.employee_personal.email.email' => 'The email must be a valid email address.',
            'records.employee_personal.email.required' => 'The email is required.',
            'records.employee_personal.email.unique' => 'The email is already taken.',

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
        ];
    }

    public function download(string $type, ?string $spec = null, ?int $key = null) {
        $file = $this->getFileFromRecord($type, $spec, $key, EmployeePersonal::class);

        if (!$file) {
            $file = $this->getFileFromRecord($type, $spec, $key, EmployeeUpdatePersonal::class);
        }

        $path = 'documents/' . $this->employee_no . '/' . $file;

        $this->setActiveAccordion($spec);

        if ($file && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        return $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Oops',
            'isRemoveRowDT' => false,
            'showAlert' => true,
            'message' => 'Unable to download, File not found!'
        ]);
    }

    private function getFileFromRecord(string $type, ?string $spec, ?int $key, string $model) {
        $record = $model::with([
            'children',
            'employment_history',
            'civil_service',
            'trainings',
            'others',
            'skills'
        ])->where('employee_no', $this->employee_no)->first();

        if (!$record) {
            return null;
        }

        return match ($type) {
            'birth_certificate' => $record->birth_certificate,
            'marriage_certificate' => $record->marriage_certificate,
            'documents' => $record->$spec[$key]['documents'] ?? null,
            default => null,
        };
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

    public function save() {

        $id = $this->employee_no;
        $record = EmployeeInformation::where('employee_no', $id)->first();
        
        if (!$record) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: Saving a non-existent employee!'
            ]);
        }

        try {
            $this->validate($this->rules($id));
        } catch (ValidationException $e) {
            $this->setErrorActiveTabAccordions($e->validator->errors()->keys());
            throw $e;
        }

        DB::beginTransaction();

        try {

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
                'message' => 'You\'re profile is now in pending for HR\'s approval. We\'ll sent you a notification once approved. Thank you!',
            ]);

            $user = EmployeeAccount::find($this->employee_id);
            $message = 'Employee <strong>' . $this->employee_no . '</strong> has submitted his/her updated <strong>profile information</strong>.';
            $redirect = route('ess.approval-profile.edit', ['approval' => $user->employee_no]);
            $user->notify(new Notifications('info', $message, $redirect, 'admin'));

            return;

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

    public function employee_personal(string $employee_no, array $data) {

        return EmployeeUpdatePersonal::updateOrCreate([
            'employee_no' => $employee_no
        ],[
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
        return EmployeeUpdateParents::updateOrCreate([
            'employee_no' => $employee_no
        ],[
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

        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeUpdateChildren::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');
        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateChildren::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {

            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateChildren::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                          'employee_no' => $employee_no,
                            'firstname' => $item['firstname'] ?? null,
                            'lastname' => $item['lastname'] ?? null, 
                            'middlename' => $item['middlename'] ?? null,
                            'birthdate' => $item['birthdate'] ?? null,
                            'documents' => $documents
                        ])->save();
                    } else {
                        EmployeeUpdateChildren::create([
                            'employee_no' => $employee_no,
                            'firstname' => $item['firstname'] ?? null,
                            'lastname' => $item['lastname'] ?? null, 
                            'middlename' => $item['middlename'] ?? null,
                            'birthdate' => $item['birthdate'] ?? null,
                            'documents' => $documents
                        ]);
                    }
                } else {
                    $record = EmployeeUpdateChildren::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'firstname' => $item['firstname'] ?? null,
                            'lastname' => $item['lastname'] ?? null, 
                            'middlename' => $item['middlename'] ?? null,
                            'birthdate' => $item['birthdate'] ?? null,
                        ])->save();
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateChildren::create([
                            'employee_no' => $employee_no,
                            'firstname' => $item['firstname'] ?? null,
                            'lastname' => $item['lastname'] ?? null, 
                            'middlename' => $item['middlename'] ?? null,
                            'birthdate' => $item['birthdate'] ?? null,
                            'documents' => $documents
                        ]);
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeUpdateChildren::create([
                    'employee_no' => $employee_no,
                    'firstname' => $item['firstname'] ?? null,
                    'lastname' => $item['lastname'] ?? null, 
                    'middlename' => $item['middlename'] ?? null,
                    'birthdate' => $item['birthdate'] ?? null,
                    'documents' => $documents
                ]);
            }

        }
    }
    
    public function employee_education(string $employee_no, array $data) {
        
        $path = 'documents/' . $employee_no;

        $existingIds = EmployeeUpdateEducation::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateEducation::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateEducation::where('id', $item['id'])
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
                    } else {
                        EmployeeUpdateEducation::create([
                            'employee_no' => $employee_no,
                            'level' => $item['level'],
                            'school_name' => $item['school_name'],
                            'course' => $item['course'],
                            'from_year' => $item['from_year'],
                            'to_year' => $item['to_year'],
                            'documents' => $documents
                        ]);
                    }
                } else {
                    $record = EmployeeUpdateEducation::where('id', $item['id'])
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
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateEducation::create([
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
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeUpdateEducation::create([
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

        $existingIds = EmployeeUpdateEmploymentHistory::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateEmploymentHistory::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateEmploymentHistory::where('id', $item['id'])
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
                    } else {
                        EmployeeUpdateEmploymentHistory::create([
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
                } else {
                    $record = EmployeeUpdateEmploymentHistory::where('id', $item['id'])
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
                EmployeeUpdateEmploymentHistory::create([
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

        $existingIds = EmployeeUpdateCivilService::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateCivilService::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateCivilService::where('id', $item['id'])
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
                    } else {
                        EmployeeUpdateCivilService::create([
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
                } else {
                    $record = EmployeeUpdateCivilService::where('id', $item['id'])
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
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateCivilService::create([
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
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeUpdateCivilService::create([
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

        $existingIds = EmployeeUpdateTrainings::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateTrainings::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateTrainings::where('id', $item['id'])
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
                    } else {
                        EmployeeUpdateTrainings::create([
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
                } else {
                    $record = EmployeeUpdateTrainings::where('id', $item['id'])
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
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateTrainings::create([
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
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeUpdateTrainings::create([
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

        $existingIds = EmployeeUpdateOtherWorks::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateOtherWorks::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateOtherWorks::where('id', $item['id'])
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
                    } else {
                        EmployeeUpdateOtherWorks::create([
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
                } else {
                    $record = EmployeeUpdateOtherWorks::where('id', $item['id'])
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
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateOtherWorks::create([
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
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeUpdateOtherWorks::create([
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

        $existingIds = EmployeeUpdateSkillsHobbies::where('employee_no', $employee_no)
            ->pluck('id')
            ->toArray();

        $dataIds = array_column($data, 'id');

        $missingIds = array_diff($existingIds, $dataIds);

        if (!empty($missingIds)) {
            EmployeeUpdateSkillsHobbies::whereIn('id', $missingIds)->delete();
        }

        foreach ($data as $item) {
            if (isset($item['id'])) {
                if($item['documents'] instanceof TemporaryUploadedFile) {
                    $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                    $record = EmployeeUpdateSkillsHobbies::where('id', $item['id'])
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
                    } else {
                        EmployeeUpdateSkillsHobbies::create([
                            'employee_no' => $employee_no,
                            'name' => $item['name'],
                            'recognition' => $item['recognition'],
                            'organization' => $item['organization'],
                            'documents' => $documents
                        ]);
                    }
                } else {
                    $record = EmployeeUpdateSkillsHobbies::where('id', $item['id'])
                        ->where('employee_no', $employee_no)
                        ->first();
                    if ($record) {
                        $record->fill([
                            'employee_no' => $employee_no,
                            'name' => $item['name'],
                            'recognition' => $item['recognition'],
                            'organization' => $item['organization'],
                        ])->save();
                    } else {
                        $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                        EmployeeUpdateSkillsHobbies::create([
                            'employee_no' => $employee_no,
                            'name' => $item['name'],
                            'recognition' => $item['recognition'],
                            'organization' => $item['organization'],
                            'documents' => $documents
                        ]);
                    }
                }
            } else {
                $documents = $this->uploadFile($employee_no, 'documents', $path, $item['documents'] ?? null);
                EmployeeUpdateSkillsHobbies::create([
                    'employee_no' => $employee_no,
                    'name' => $item['name'],
                    'recognition' => $item['recognition'],
                    'organization' => $item['organization'],
                    'documents' => $documents
                ]);
            }
        }

    }
    

    public function render()
    {
        return view('livewire.employee.profile');
    }
}
