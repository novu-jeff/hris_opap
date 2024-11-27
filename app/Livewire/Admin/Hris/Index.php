<?php

namespace App\Livewire\Admin\Hris;

use App\Helper\Generate;
use App\Http\Controllers\Admin\Services\EmployeeUploadService;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Imports\EmployeeImports;
use App\Models\Branches;
use App\Models\Departments;
use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\JobCategory;
use App\Models\OtherEarnings;
use App\Models\Positions;
use App\Models\Sections;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{

    use WithFileUploads;

    public $selected_id;
    public $employees;
    public $isParsing;
    public bool $isUploading = false;
    public array $records;
    public object $positions;
    public object $sections;
    public bool $isDualCitizenship = false;
    public array $countries;
    public string $activeTab = 'details';
    public $file;
    public $upload_preview;
    public $activeAccordion;
    public $jobCategories;
    public $resultMessage;

    protected $listeners = ['loadRecords', 'remove'];

    public function mount() {

        if(Session::has('target')) {
            $data = session('target');
            if(array_key_exists('id', $data)) {
                return $this->loadRecords($data['id']);
            }
        
        } else {
            $this->loadRecords();
        }
    }

    public function loadRecords(int $employee_no = null) {

        $model = EmployeeInformation::class;

        $this->sections = Sections::all();
        $this->positions = Positions::all();
        $this->jobCategories = JobCategory::all();

        if (!is_null($employee_no)) {
            
            // When a specific employee is selected
            $this->selected_id = $employee_no;

            // Fetch related earnings and deductions
            $inst = new OtherServices();

            $earnings = $inst->earnings($employee_no);
            $deductions = $inst->deductions($employee_no);

            // Fetch employee data
            $data = $model::with([
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
            ])->where('employee_no', $employee_no)->first();

            // If no data is found, reset the records and return
            if (!$data) {
                $this->records = [];
                $this->dispatch('reinitializeDataTable');
                return;
            }

            // Populate records with employee information
            $this->records = [
                'employee_information' => [
                    'id' => $data->id,
                    'employee_id' => format_id($data->id, 6),
                    'employee_no' => $data->employee_no,
                    'biometrics_id' => $data->biometrics_id,
                    'section_id' => $data->section_id,
                    'position_id' => $data->position_id,
                    'date_hired' => $data->date_hired,
                    'service_duration' => relative_time_duration($data->date_hired),
                    'date_resignation' => $data->date_resignation,
                    'type' => $data->job_category_id,
                    'status' => $data->status,
                    'salary_method' => $data->salary_method,
                    'leave_credits' => $data->leave_credits,
                    'monthly_rate' => $data->monthly_rate,
                    'payroll_account_number' => $data->payroll_account_number,
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
                'employee_education' => $data->education->isNotEmpty() ? $data->education->toArray() : [],
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
                'employee_children' => $data->children->isNotEmpty() ? $data->children->toArray() : [],
                'employee_employment_history' => $data->employment_history->isNotEmpty() ? $data->employment_history->toArray() : [],
                'employee_civil_service' =>  $data->civil_service->isNotEmpty() ? $data->civil_service->toArray() : [],
                'employee_trainings' =>  $data->trainings->isNotEmpty() ? $data->trainings->toArray() : [],
                'employee_others' =>  $data->others->isNotEmpty() ? $data->others->toArray() : [],
                'employee_skills' =>  $data->skills->isNotEmpty() ? $data->skills->toArray() : [],

                'employee_gsis' => $data->personal->gsis_item ? $data->personal->gsis_item->toArray() : [],
                'other_earnings' => $earnings ?? [],
                'other_deductions' => $deductions ?? [],
                
            ];

            if(!is_null($data->section_id)) {
                $this->select_change('section');
            } 
            
            $this->employees = [];

            return;
            
        } else {
            $this->employees = $model::with('personal')->get();
            $this->records = [];

            return;

        }
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
    
    public function view(int $id) {
        Session::put('target', [
            'id' => $id,
        ]);
        return redirect()->route('hris.index'); 
    }

    public function go_back() {
        session()->forget('target');
        return redirect()->route('hris.index');
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

    public function uploadRecords() {
        $this->dispatch('showModal', [
            'modal' => 'upload_employee'
        ]);
        $this->jobCategories = JobCategory::all();
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

    public function close_upload_employee() {
        $this->reset('upload_preview', 'file');
    }

    public function updatedFile() {
        if ($this->file) {
            $this->resetErrorBag('file');
            $this->upload_preview;
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, ['xls', 'xlsx'])) {
                    try {

                        $files = Storage::files('public/temp/files');
                        Storage::delete($files); 

                        $fileName = uniqid() . '.' . $extension;

                        $file->storeAs('public/temp/files', $fileName);

                        $this->upload_preview = asset('storage/temp/files/' . $fileName);

                        $this->isParsing = false;

                    } catch (\Exception $e) {
                        $this->addError('file', 'There was an error saving the file to temporary storage.');
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

    public function upload_file() {

        $this->isUploading = true;
    
        DB::beginTransaction();
    
        try {
            // Correct file path using the Storage facade
            $relativePath = str_replace(asset('storage/'), '', $this->upload_preview);
            $absolutePath = storage_path('app/public/' . $relativePath);
    
            // Check if the file exists in the storage
            if (!Storage::exists('public/' . $relativePath)) {
                throw new \Exception('File does not exist in storage.');
            }
    
            // Load the Excel file and get sheet names
            $spreadsheet = IOFactory::load($absolutePath);
            $sheetNames = $spreadsheet->getSheetNames(); // Get sheet names
    
            // Load the Excel file to an array
            $sheetsData = Excel::toArray(new EmployeeImports, $absolutePath);
    
            $this->validateUploaded($spreadsheet, $sheetNames);

            $results = [];

            foreach ($sheetsData as $index => $sheet) {
                // Remove the first row as it contains labels
                $sheet = array_slice($sheet, 1);
            
                // Filter out empty rows
                $sheet = array_filter($sheet, function ($row) {
                    return !empty(array_filter($row, fn($value) => $value !== null && $value !== ''));
                });
            
                // Reset array keys
                $sheet = array_values($sheet);
            
                // Get sheet name for processing logic
                $sheetName = $sheetNames[$index];
            
                $service = new EmployeeUploadService;
            
                // Process each sheet based on its name
                $result = match ($sheetName) {
                    'Employee Information' => $service->uploadEmployeeInformation($sheet),
                    'Family Background' => $service->uploadFamilyBackground($sheet),
                    'Children' => $service->uploadChildren($sheet),
                    'Education' => $service->uploadEducation($sheet),
                    'Employment History' => $service->uploadEmploymentHistory($sheet),
                    'Civil Service' => $service->uploadCivilService($sheet),
                    'Trainings' => $service->uploadTrainings($sheet),
                    'Other Works' => $service->uploadOtherWorks($sheet),
                    'Skills' => $service->uploadSkills($sheet),
                    default => null
                };
            
                // After processing, check if the sheet is "Employee Information"
                if ($sheetName === 'Employee Information') {
                    // Check if 'employee_information' exists in the result and store relevant data
                    if (isset($result['employee_information'])) {
                        $results['Employee Information'] = [
                            'inserted' => $result['employee_information']['inserted'],
                            'updated' => $result['employee_information']['updated']
                        ];
                        // Remove 'employee_information' from the result
                        unset($result['employee_information']);
                    }
            
                    // Separate the Employee Personal data from the result
                    if (isset($result['employee_personal'])) {
                        $results['Employee Personal'] = [
                            'inserted' => $result['employee_personal']['inserted'],
                            'updated' => $result['employee_personal']['updated']
                        ];
                        // Remove 'employee_personal' from the result
                        unset($result['employee_personal']);
                    }
                } else {
                    // For other sheets, store them normally
                    $results[$sheetName] = $result;
                }
            }
            
            // Define a list of sheet names to exclude from the result message
            $excludeSheets = ['Options']; // Add any other sheet names to exclude here
            
            // Iterate through each section in the results
            foreach ($results as $section => $data) {
                // Skip sections listed in $excludeSheets
                if (in_array($section, $excludeSheets)) {
                    continue;
                }

                $insertedCount = isset($data['inserted']['total']) ? $data['inserted']['total'] : 0;
                $updatedCount = isset($data['updated']['total']) ? $data['updated']['total'] : 0;
            
                // Start the message for each section
                $message = "$section was added (" . (isset($data['inserted']['total']) ? $data['inserted']['total'] : 0) . ") records or updated (" . (isset($data['updated']['total']) ? $data['updated']['total'] : 0) . ") records";
            
                // Check if 'inserted' is an array and has 'data'
                if (isset($data['inserted']) && is_array($data['inserted']) && isset($data['inserted']['data']) && is_array($data['inserted']['data']) && !empty($data['inserted']['data'])) {
                    $insertedData = array_map(function ($item) {
                        $employeeNo = $item['employee_no'] ?? null;
                
                        if ($employeeNo) {
                            $employee = EmployeePersonal::where('employee_no', $employeeNo)->first();
                            $name = $employee ? $employee->firstname . ' ' . $employee->lastname : 'No name';
                        } else {
                            $name = 'No employee number';
                        }
                
                        return [
                            'employee_no' => $employeeNo,
                            'name' => $name,
                            'message' => "Employee # {$employeeNo} - {$name}"
                        ];
                    }, $data['inserted']['data']);
                } else {
                    $insertedData = []; // No records updated
                }                
            
                // Check if 'updated' is an array and has 'data'
                if (isset($data['updated']) && is_array($data['updated']) && isset($data['updated']['data']) && is_array($data['updated']['data']) && !empty($data['updated']['data'])) {
                    $updatedData = array_map(function ($item) {
                        // Check if 'employee_no' and 'name' exist before trying to access them
                        $employeeNo = $item['employee_no'] ?? 'No employee number';
                        $name = $item['name'] ?? 'No name';
                        return [
                            'employee_no' => $employeeNo,
                            'name' => $name,
                            'message' => "Employee # {$employeeNo} - {$name}",
                        ];
                    }, $data['updated']['data']);
                } else {
                    $updatedData = []; // No records updated
                }
            
                // Store the data in the resultMessage array for later rendering
                $resultMessage[] = [
                    'section' => $section,
                    'insertedList' => $insertedData,
                    'updatedList' => $updatedData,
                    'insertedCount' => $insertedCount, 
                    'updatedCount' => $updatedCount,  
                ];
            
                $this->resultMessage = $resultMessage;
            }            
                  
            DB::commit();
    
            $this->dispatch('hideModal', [
                'modal' => 'upload_employee'
            ]);

            $this->dispatch('showModal', [
                'modal' => 'alert_employee'
            ]);

            $this->loadRecords();

        } catch (\Exception $e) {
            
            DB::rollBack();
    
            logger()->error('Error uploading file: ' . $e->getMessage());
    
            // Dispatch error message to frontend
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        } finally {
            // Ensure `isUploading` is set to false
            $this->isUploading = false;
        }
    }

    public function validateUploaded($spreadsheet, $sheetNames) {

        $expectedSheets = [
            'employee information' => ['employee no.', 'lastname', 'firstname', 'middlename', 'address', 'sex', 'civil status', 'birthday', 'age', 'gsis no (bp no.)', 'pagibig id', 'sss id', 'phic id', 'tin id', 'bank account no.', 'date hired', 'position', 'monthly salary', 'job category', 'email'],
            'family background' => ['employee no.', 'spouse surname', 'spouse firstname', 'spouse middlename', 'spouse suffix', 'spouse occupation', 'spouse business name', 'spouse business address', 'spouse contact no', 'father surname', 'father firstname', 'father middlename', 'father suffix', 'mother surname', 'mother firstname', 'mother middlename'],
            'children' => ['employee no.', 'firstname', 'middlename', 'lastname', 'birthdate'],
            'education' => ['employee no.', 'level', 'school name', 'course', 'from year', 'to year'],
            'employment history' => ['employee no.', 'position', 'department', 'company name', 'monthly salary', 'employment status', 'is government?', 'from year', 'to year'],
            'civil service' => ['employee no.', 'certification', 'rating', 'date exam', 'place exam', 'license no', 'date validity'],
            'trainings' => ['employee no.', 'type', 'name', 'date from', 'date to', 'consumed hours', 'sponsored by'],
            'other works' => ['employee no.', 'organization', 'address', 'date from', 'date to', 'consumed hours', 'position'],
            'skills' => ['employee no.', 'skill / hobbies name', 'recognition', 'organization'],
            'options' => ['job categories', 'bool', 'civil status', 'sex', 'departments']
        ];
    
        foreach ($sheetNames as $sheetName) {
    
            $sheetNameLower = strtolower($sheetName);  
    
            if (array_key_exists($sheetNameLower, $expectedSheets)) {
                $sheetData = $spreadsheet->getSheetByName($sheetName)->toArray();
                
                // Remove null values from each row without removing the entire row
                $sheetData = array_map(function($row) {
                    return array_filter($row, function($value) {
                        return $value !== null;  // Keep only non-null values
                    });
                }, $sheetData);
            
                // Check if the sheet data has rows and extract the first row for header
                if (empty($sheetData)) {
                    throw new \Exception("Sheet '{$sheetName}' is empty.");
                }
            
                $header = $sheetData[0];
            
                // Trim spaces and convert the header values to lowercase for comparison
                $headerLower = array_map(function($item) {
                    return strtolower(trim($item)); // Remove leading/trailing spaces and convert to lowercase
                }, $header);
            
                // Ensure the expected header also has trimmed values
                $expectedHeader = array_map('strtolower', array_map('trim', $expectedSheets[$sheetNameLower]));
            
                if ($headerLower !== $expectedHeader) {
                    Log::error("Invalid header in sheet '{$sheetName}'. Expected: " . implode(', ', $expectedHeader) . ". Found: " . implode(', ', $headerLower));
                    throw new \Exception("Uploaded file contains invalid format");
                }
            } else {
                Log::error("Unexpected sheet '{$sheetName}' found in the file.");
                throw new \Exception("Uploaded file contains invalid format");
            }
            
        }
    
        return true;
    }
    
    
    
    private function sanitizeUser(array $user): array {
        return [
            'employee_no' => $user[0] ?? null,
            'lastname' => strtolower($user[1] ?? ''),
            'firstname' => strtolower($user[2] ?? ''),
            'middlename' => strtolower($user[3] ?? ''),
            'present_address' => $user[4] ?? null,
            'sex' => strtolower($user[5] ?? ''),
            'civil_status' => strtolower($user[6] ?? ''),
            'birthday' => $user[7] ?? null,
            'age' => $user[8] ?? null,
            'pagibig_no' => $user[9] ?? null,
            'sss_no' => $user[10] ?? null,
            'philhealth_no' => $user[11] ?? null,
            'tin_no' => $user[12] ?? null,
            'bank_account_no' => $user[13] ?? null,
            'date_hired' => $user[14] ?? null,
            'position' => $user[15] ?? '',
            'department' => $user[16] ?? '',
            'type' => $user[17] ?? null,
            'email' => strtolower($user[18] ?? ''),
        ];
    }
    
    private function createEmployeeInformation(array $user, int $positionId, int $departmentId): EmployeeInformation {
        

        $category_id = JobCategory::where('name', $user['type'])->first()->id;

        return EmployeeInformation::create([
            'employee_no' => $user['employee_no'],
            'job_category_id' => $category_id,
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

        $id = $this->selected_id;

        $record = EmployeeInformation::where('employee_no', $id);
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
                'message' => 'Employee #' . $id . ' records successfully.' 
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

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeInformation::where('employee_no', $this->selected_id)->first();
                
            if($record) {
                
                $record->account()->delete();
                $record->personal()->delete();
                $record->education()->delete();
                $record->parents()->delete();
                $record->children()->delete();
                $record->employment_history()->delete();
                $record->civil_service()->delete();
                $record->trainings()->delete();
                $record->others()->delete();
                $record->skills()->delete();
                $record->messages()->delete();
            
                $record->delete();

                $this->loadRecords();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Employee #' . strtoupper($record->employee_no) . ' was deleted successfully' 
                ]);

            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }
    }


    public function render()
    {
        return view('livewire.admin.hris.index');
    }
}
