<?php

namespace App\Livewire\Admin\Hris;

use App\Helper\Generate;
use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Mail\SendExistingEmployeeAccount;
use App\Models\Branches;
use App\Models\DepartmentCenters;
use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\Positions;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{

    use WithFileUploads;

    public $selected_id;
    public $employees;
    public $isParsing;
    public bool $isUploading = false;
    public array $records;
    public object $departments;
    public object $branches;
    public object $positions;
    public bool $isDualCitizenship = false;
    public array $countries;
    public string $activeTab = 'details';
    public $file;
    public $upload_preview;
    public $activeAccordion;

    protected $listeners = ['loadRecords'];

    public function boot() {
        if(Session::has('target')) {
            $data = session('target');
            if(array_key_exists('id', $data)) {
                $this->loadRecords($data['id']);
            }
            
            session()->forget('target');

        }
    }

    public function mount() {
        // $this->loadRecords(47);
        // $this->loadCountries();
    }

    public function loadRecords(int $id = null) {

        $model = EmployeeInformation::class;

        $this->branches = Branches::all();
        $this->departments = DepartmentCenters::all();
        $this->positions = Positions::all();


        if(!is_null($id)) {
            
            $this->selected_id = $id;

            $data = $model::with(
                [
                    'personal', 
                    'account', 
                    'education',
                    'parents',
                    'children',
                    'employment_history'
                ])->where('id', $id)->first();
        
            $records = [
                'employee_information' => [
                    'id'  => $data->id,
                    'employee_id'  => format_id($data->id, 6),
                    'employee_no'  => $data->employee_no,
                    'biometrics_id' => $data->biometrics_id ?? null,
                    'department_id' => $data->department_id ?? null,
                    'branch_id' => $data->branch_id ?? null,
                    'position_id' => $data->position_id ?? null,
                    'date_hired' => $data->date_hired ?? null,
                    'date_resignation' => $data->date_resignation ?? null,
                    'type' => $data->type ?? null,
                    'status' => $data->status ?? null,
                    'salary_method' => $data->salary_method ?? null,
                    'leave_credits' => $data->leave_credits ?? null,
                    'monthly_rate' => $data->monthly_rate ?? null,
                    'payroll_account_number' => $data->payroll_account_number ?? null,
                ],
                'employee_account' => [
                    'email' => $data->account->email ?? null,
                ],
                'employee_personal' => [
                    'profile' => $data->personal->profile ?? null,
                    'firstname' => $data->personal->firstname ?? null,
                    'middlename' => $data->personal->middlename ?? null,
                    'lastname' => $data->personal->lastname ?? null,
                    'suffix' => $data->personal->suffix ?? null,
                    'birthday' => $data->personal->birthday ?? null,
                    'civil_status' => $data->personal->civil_status ?? null,
                    'sex' => $data->personal->sex ?? null,
                    'citizenship' => $data->personal->citizenship ?? null,
                    'citizenship_type' => $data->personal->citizenship_type ?? null,
                    'country' => $data->personal->country ?? null,
                    'present_address' => $data->personal->present_address ?? null,
                    'present_province' => $data->personal->present_province ?? null,
                    'present_city' => $data->personal->present_city ?? null,
                    'permanent_address' => $data->personal->permanent_address ?? null,
                    'permanent_province' => $data->personal->permanent_province ?? null,
                    'permanent_city' => $data->personal->permanent_city ?? null,
                    'mobile_number' => $data->personal->mobile_number ?? null,
                    'tel_no' => $data->personal->tel_no ?? null,
                    'company_email' => $data->personal->company_email ?? null,
                    'height' => $data->personal->height ?? null,
                    'weight' => $data->personal->weight ?? null,
                    'blood_type' => $data->personal->blood_type ?? null,
                    'gsis_no' => $data->personal->gsis_no ?? null,
                    'pagibig_no' => $data->personal->pagibig_no ?? null,
                    'philhealth_no' => $data->personal->philhealth_no ?? null,
                    'sss_no' => $data->personal->sss_no ?? null,
                    'tin_no' => $data->personal->tin_no ?? null,
                ],
                'employee_education' => $data->education->isEmpty() ? [] : $data->education->toArray(),
                'employee_parents' => [
                    'spouse_surname' => $data->parents->spouse_surname ?? null,
                    'spouse_firstname' => $data->parents->spouse_firstname ?? null,
                    'spouse_middlename' => $data->parents->spouse_middlename ?? null,
                    'spouse_suffix' => $data->parents->spouse_suffix ?? null,
                    'spouse_occupation' => $data->parents->spouse_occupation ?? null,
                    'spouse_business_name_employer' => $data->parents->spouse_business_name_employer ?? null,
                    'spouse_business_address' => $data->parents->spouse_business_address ?? null,
                    'spouse_contact_no' => $data->parents->spouse_contact_no ?? null,
                    'father_surname' => $data->parents->father_surname ?? null,
                    'father_firstname' => $data->parents->father_firstname ?? null,
                    'father_middlename' => $data->parents->father_middlename ?? null,
                    'father_suffix' => $data->parents->suffix ?? null,
                    'mother_surname' => $data->parents->mother_surname ?? null,
                    'mother_firstname' => $data->parents->mother_firstname ?? null,
                    'mother_middlename' => $data->parents->mother_middlename ?? null,
                ],
                'employee_children' => $data->children->isEmpty() ? [] : $data->children->toArray(),
                'employee_employment_history' => $data->employment_history->isEmpty() ? [] : $data->employment_history->toArray(),
            ];
        
            $this->records = $records;
        
            return $this->dispatch('hideModal', [
                'modal' => 'select_employee', 
            ]);
        }

        $this->employees = $model::with('personal')->get();


        return $this->dispatch('showModal', [
            'modal' => 'select_employee', 
        ]);
    }

    public function loadCountries() {
        $client = new Client();
        $response = $client->get('https://restcountries.com/v3.1/all?fields=name');
    
        $countries = json_decode($response->getBody()->getContents(), true);
    
        usort($countries, function ($a, $b) {
            return strcmp($a['name']['common'], $b['name']['common']);
        });

        return $this->countries = $countries;
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
            if($this->records['employee_personal']['citizenship'] == 'dual_citizenship') {
                $this->isDualCitizenship = true;
            } else {
                $this->isDualCitizenship = false;
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
            ]
        ];
    
        $this->records[$type][] = $fields[$type];
    }

    public function uploadRecords() {
        $this->dispatch('showModal', [
            'modal' => 'upload_employee'
        ]);
    }
    
    public function removeRecord($tab, $type, $index) {
        $this->activeTab = $tab;
        
        if (isset($this->records[$type][$index])) {
            unset($this->records[$type][$index]);
            $this->records[$type] = array_values($this->records[$type]);
        }
    }

    protected function rules(int $id = null) {
        return [
            'records.employee_information.type' => 'nullable|in:freelance,part time,contractual,project based,regular,probationary',
            'records.employee_information.status' => 'nullable|in:active,inactive',
            'records.employee_information.position_id' => 'nullable|exists:positions,id',
            'records.employee_information.branch_id' => 'nullable|exists:branches,id',
            'records.employee_information.department_id' => 'nullable|exists:department_centers,id',
            'records.employee_information.salary_method' => 'nullable|in:cash,bank transfer,paycheck,e-wallet',
            'records.employee_information.employee_no' => 'nullable',
            'records.employee_information.biometrics_id' => 'nullable|numeric',
            
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

            'records.employee_account.password' => 'nullable|min:8|same:records.employee_account.confirm_password',
            'records.employee_account.confirm_password' => 'required_with:records.employee_account.password|min:8'

        ];
    }

    public function messages() {
        return [
            'records.employee_information.type.in' => 'The employment type must be one of the following: freelance, part time, contractual, project based, regular, or probationary.',
            'records.employee_information.status.in' => 'The status must be either active or inactive.',
            'records.employee_information.position_id.exists' => 'The selected position does not exist.',
            'records.employee_information.branch_id.exists' => 'The selected branch does not exist.',
            'records.employee_information.department_id.exists' => 'The selected department does not exist.',
            'records.employee_information.salary_method.in' => 'The salary method must be one of the following: cash, bank transfer, paycheck, or e-wallet.',

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
            'records.employee_account.password.max' => 'The password must be at least 8 characters.',
            'records.employee_account.password.same' => 'The password and confirmation password must match.',
            'records.employee_account.confirm_password.required_with' => 'The confirm password field is required.',
            'records.employee_account.confirm_password.max' => 'The confirm password must be at least 8 characters.',
        ];
    }

    public function save(int $id) {

        $record = EmployeeInformation::find($id);
        if(!$record) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops', 
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: The save ID does not exists.'
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

            $process = new HRISProcessingService;
            $process->save(false, $id, $this->selected_id, $this->records);
            DB::commit();

            return$this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee records updated successfully.' 
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


    public function updatedFile()
    {
    
        if ($this->file) {

            $this->resetErrorBag('file');
            $this->upload_preview = [];
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, ['xls', 'xlsx'])) {
                    try {
                        $data = Excel::toArray([], $file, null, \Maatwebsite\Excel\Excel::XLSX)[0];

                        $this->upload_preview[] = array_map(function ($row) {
                            return array_map(function ($cell, $key) {
                                if (in_array($key, [8, 15])) {
                                    if (is_numeric($cell)) {
                                        try {
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cell);
                                            return $date->format('Y-m-d'); 
                                        } catch (\Exception $e) {
                                            return $cell; // Fallback to original cell value
                                        }
                                    }
                                }

                                return is_string($cell) ? strtolower($cell) : $cell;
                            }, $row, array_keys($row));
                        }, array_filter(array_slice($data, 1), function ($row) {
                            return !empty(array_filter($row)); 
                        }));

                        $this->isParsing = false;
                    } catch (\Exception $e) {
                        $this->addError('file', 'There was an error reading the Excel file.');
                        $this->isParsing = false; 
                    }
                } else {
                    $this->addError('file', 'The file must be an Excel file (.xls or .xlsx).');
                }
            }

            $this->file = null;
        } else {
            $this->isParsing = true;
        }


    }


    public function upload_file()
    {
        $this->isUploading = true;

        DB::beginTransaction();
    
        try {
            // Define mappings for better readability
            foreach ($this->upload_preview[0] as $user) {
                $user = $this->sanitizeUser($user);
    
                // Create or fetch related data
                $position = Positions::firstOrCreate(['name' => strtolower($user['position'])]);
                $department = DepartmentCenters::firstOrCreate([
                    'name' => strtolower($user['department']),
                    'cost_center_id' => 1
                ]);
    
                // Create Employee Information
                $employeeInformation = $this->createEmployeeInformation($user, $position->id, $department->id);
    
                // Create Employee Personal Information
                $employeePersonal = $this->createEmployeePersonal($user, $employeeInformation->id);
    
                // Generate and Create Employee Account
                $email = app('App\Helper\Generate')->email($employeeInformation->id, $employeePersonal->firstname, $employeePersonal->lastname);
                $password = app('App\Helper\Generate')->password();
    
                EmployeeAccount::create([
                    'employee_id' => $employeeInformation->id,
                    'email' => $email,
                    'password' => $password['hashed'],
                ]);
            }
    
            // Commit database changes after all records are processed
            DB::commit();
    
            // Dispatch success message to frontend
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => count($this->upload_preview[0]) . ' employee/s has been added.'
            ]);
    
            // Reset variables after upload
            $this->reset('upload_preview', 'file');
        } catch (\Exception $e) {
            // Rollback database changes if any error occurs
            DB::rollBack();
    
            // Log the error
            logger()->error('Error uploading file: ' . $e->getMessage());
    
            // Dispatch error message to frontend
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    
        // Set isUploading to false after upload process is complete (either success or failure)
        $this->isUploading = false;
        
    }
    
    
    private function sanitizeUser(array $user): array {
        return [
            'company_name' => $user[0] ?? null,
            'employee_no' => $user[1] ?? null,
            'lastname' => strtolower($user[2] ?? ''),
            'firstname' => strtolower($user[3] ?? ''),
            'middlename' => strtolower($user[4] ?? ''),
            'present_address' => $user[5] ?? null,
            'sex' => strtolower($user[6] ?? ''),
            'civil_status' => strtolower($user[7] ?? ''),
            'birthday' => $user[8] ?? null,
            'age' => $user[9] ?? null,
            'pagibig_no' => $user[10] ?? null,
            'sss_no' => $user[11] ?? null,
            'philhealth_no' => $user[12] ?? null,
            'tin_no' => $user[13] ?? null,
            'bank_account_no' => $user[14] ?? null,
            'date_hired' => $user[15] ?? null,
            'position' => $user[16] ?? '',
            'department' => $user[17] ?? '',
            'type' => $user[18] ?? null,
            'email' => strtolower($user[19] ?? ''),
        ];
    }
    
    private function createEmployeeInformation(array $user, int $positionId, int $departmentId): EmployeeInformation {
        return EmployeeInformation::create([
            'company_name' => $user['company_name'],
            'employee_no' => $user['employee_no'],
            'type' => $user['type'],
            'date_hired' => $user['date_hired'],
            'bank_account_no' => $user['bank_account_no'],
            'position_id' => $positionId,
            'department_id' => $departmentId,
        ]);
    }
    
    private function createEmployeePersonal(array $user, int $employeeId): EmployeePersonal {
        return EmployeePersonal::create([
            'employee_id' => $employeeId,
            'email' => $user['email'],
            'firstname' => $user['firstname'],
            'middlename' => $user['middlename'],
            'lastname' => $user['lastname'],
            'birthday' => $user['birthday'],
            'age' => $user['age'],
            'sex' => $user['sex'],
            'civil_status' => $user['civil_status'],
            'present_address' => $user['present_address'],
            'sss_no' => $user['sss_no'],
            'pagibig_no' => $user['pagibig_no'],
            'philhealth_no' => $user['philhealth_no'],
            'tin_no' => $user['tin_no'],
        ]);
    }
    

    public function remove_upload($index) {
        if (isset($this->upload_preview[0][$index])) {
            unset($this->upload_preview[0][$index]);            
            $this->upload_preview[0] = array_values($this->upload_preview[0]);
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

    public function render()
    {
        return view('livewire.admin.hris.index');
    }
}
