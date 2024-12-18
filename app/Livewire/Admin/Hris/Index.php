<?php

namespace App\Livewire\Admin\Hris;

use App\Http\Controllers\Admin\Services\EmployeeUploadService;
use App\Imports\EmployeeImports;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeUpdatePersonal;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{

    use WithFileUploads;

    public $employee_no;
    public $employees;
    public $isParsing;
    public bool $isUploading = false;
    public $file;
    public $upload_preview;
    public $resultMessage;
    public $countries;
    public $selected_id;
    public $isLinkSchedule = false;
    public $shifts;
    public $schedules;
    public $shift_id;
    public $schedule_id;

    public bool $lazy = true;

    protected $listeners = ['remove', 'loading'];

    public function mount() {
        $this->loadRecords();
    }

    public function loading() {
        $this->dispatch('reinitializeDataTable');
        $this->lazy = false;
    }

    public function loadRecords() {
        $this->employees = EmployeeInformation::with('personal')->get();
        $this->shifts = ShiftSchedule::all();
        $this->schedules = EmployeeSchedule::all();
    }

    public function close_upload_employee() {
        $this->dispatch('reinitializeDataTable');
        $this->reset('upload_preview', 'file');
    }

    public function select_change($property) {
        if($property === 'linkSchedule') {
            $this->isLinkSchedule = !$this->isLinkSchedule ? false : true;
            $this->dispatch('reinitializeDataTable');
        }
    }

    public function updatedFile() {
        if ($this->file) {
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

        $this->dispatch('reinitializeDataTable');

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

            $schedules = [
                'shift' => $this->shift_id,
                'schedule' => $this->schedule_id
            ];

            $results = [];

            foreach ($sheetsData as $index => $sheet) {
                // Remove the first row as it contains labels
                $sheet = array_slice($sheet, 1);
            
                // Filter out empty rows
                $sheet = array_filter($sheet, function ($row) {
                    return isset($row[0]) && !empty($row[0]) && 
                           !empty(array_filter($row, fn($value) => $value !== null && $value !== ''));
                });
            
                // Reset array keys
                $sheet = array_values($sheet);
            
                // Get sheet name for processing logic
                $sheetName = $sheetNames[$index];
            
                $service = new EmployeeUploadService;
            
                // Process each sheet based on its name
                $result = match ($sheetName) {
                    'Employee Information' => $service->uploadEmployeeInformation($sheet, $schedules),
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
                            'message' => "Employee  {$employeeNo} - {$name}"
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

            $this->reset(['shift_id', 'schedule_id']);

            $this->loadRecords();
            $this->dispatch('reinitializeDataTable');

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
            'employee information' => ['employee no.', 'bsd no.', 'lastname', 'firstname', 'middlename', 'address', 'sex', 'civil status', 'birthday', 'age', 'gsis no (bp no.)', 'pagibig id', 'sss id', 'phic id', 'tin id', 'bank account no.', 'date hired', 'position', 'monthly salary', 'job category', 'email'],
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
    
    public function remove(bool $isNotify = true, string $employee_no = null) {

        $this->dispatch('reinitializeDataTable');

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'remove';

            $this->selected_id = $employee_no;
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

                $record = EmployeeUpdatePersonal::where('employee_no', $this->employee_no)->first();

                $this->loadRecords();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Employee ' . strtoupper($record->employee_no) . ' was deleted successfully' 
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

            $record = EmployeeUpdatePersonal::where('employee_no', $this->employee_no)->first();

            if($record) {

                $record->education()->delete();
                $record->parents()->delete();
                $record->children()->delete();
                $record->employment_history()->delete();
                $record->civil_service()->delete();
                $record->trainings()->delete();
                $record->others()->delete();
                $record->skills()->delete();
                
                $record->delete();

                return true;

            }
        }
    }

    public function render()
    {
        return view('livewire.admin.hris.index');
    }

}
