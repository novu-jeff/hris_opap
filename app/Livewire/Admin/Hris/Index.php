<?php

namespace App\Livewire\Admin\Hris;

use App\Http\Controllers\Admin\Services\EmployeeUploadService;
use App\Imports\EmployeeImports;
use App\Jobs\EmployeeUploadJob;
use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeUpdatePersonal;
use App\Models\EmployementTypes;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $employee_no;
    public $isParsing;
    public $isUploading = false;
    public $file;
    public $upload_preview;
    public $resultMessage;
    public $countries;
    public $selected_id;
    public $isLinkSchedule = false;
    public $shifts;
    public $schedules;
    public $roles;
    public $shift_id;
    public $schedule_id;
    public $employmentTypes;
    public $selectedType;

    public bool $lazy = true;

    protected $listeners = ['remove', 'unlock', 'restore', 'loading', 'loadRecords'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function mount() {
        $this->loadRecords();
    }

    public function loading() {
        $this->lazy = false;
    }

    public function loadRecords() {
        $this->shifts = ShiftSchedule::all();
        $this->schedules = EmployeeSchedule::all();
        $this->roles = EmployementTypes::all();
        $this->employmentTypes = EmployementTypes::all();
    }

    public function close_upload_employee() {
        $this->reset('upload_preview', 'file', 'isLinkSchedule');
    }

    public function select_change($property) {
        if($property === 'linkSchedule') {
            $this->isLinkSchedule = !$this->isLinkSchedule ? false : true;
        }
    }

    public function updatedFile() {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

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

    }

    public function upload_file()
    {
        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->isUploading = true;

        try {
            $relativePath = str_replace(asset('storage/'), '', $this->upload_preview);
            $absolutePath = storage_path('app/public/' . $relativePath);

            if (!Storage::exists('public/' . $relativePath)) {
                throw new \Exception('File does not exist in storage.');
            }

            $spreadsheet = IOFactory::load($absolutePath);
            $sheetNames = $spreadsheet->getSheetNames();
            $sheetsData = Excel::toArray(new EmployeeImports, $absolutePath);

            $this->validateUploaded($spreadsheet, $sheetNames);

            $schedules = [
                'shift' => $this->shift_id,
                'schedule' => $this->schedule_id
            ];

            $jobs = [];

            foreach ($sheetsData as $index => $sheet) {
                $sheetName = $sheetNames[$index];

                $sheet = array_slice($sheet, 1);
                $sheet = array_filter($sheet, fn($row) =>
                    isset($row[0]) && !empty($row[0]) &&
                    !empty(array_filter($row, fn($v) => $v !== null && $v !== ''))
                );
                $sheet = array_values($sheet);

                $chunks = array_chunk($sheet, 100);
                foreach ($chunks as $chunk) {
                    $jobs[] = new EmployeeUploadJob($chunk, $sheetName, $schedules);
                }
            }

            Bus::batch($jobs)->dispatch();

            $this->dispatch('hideModal', [
                'modal' => 'upload_employee'
            ]);

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => 'Upload is being processed in the background.',
            ]);

            $this->reset(['shift_id', 'schedule_id', 'isLinkSchedule']);
            $this->loadRecords();

        } catch (\Exception $e) {

            logger()->error('Error uploading file: ' . $e->getMessage());

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        } finally {
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

    public function remove(bool $isNotify = true, ? string $employee_no = null) {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete employee <b>' . strtoupper($employee_no) . '</b>. Once this action is completed, it cannot be undone or reversed!';
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

                $record->isDeleted = true;
                $record->save();

                $record = EmployeeUpdatePersonal::where('employee_no', $this->employee_no)->first();

                $this->loadRecords();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Employee ' . strtoupper($this->selected_id) . ' was deleted successfully.'
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

    public function unlock(bool $isNotify = true, ? string $employee_no = null) {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that this account has been locked due to multiple login attempts. Are you sure to unlock account  <b>' . strtoupper($employee_no) . '?</b>. Once this action is completed, it cannot be undone or reversed!';
            $action = 'unlock';

            $this->selected_id = $employee_no;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeAccount::where('employee_no', $this->selected_id)->first();

            if($record) {

                $record->isLocked = false;
                $record->login_attempts = 0;
                $record->save();

                $this->loadRecords();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Employee ' . strtoupper($this->selected_id) . ' account has been unlocked.'
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

    public function restore(bool $isNotify = true, ? string $employee_no = null) {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that this archived account will be restored. Once this action is completed, it cannot be undone or reversed!';
            $action = 'restore';

            $this->selected_id = $employee_no;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeInformation::where('employee_no', $this->selected_id)
                ->first();

            if($record) {

                $record->isDeleted = false;
                $record->save();

                $this->loadRecords();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Employee ' . strtoupper($this->selected_id) . ' account has been restored.'
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

    public function changeEmployeeNo($employee_no) {
        $this->dispatch('showModal', [
            'modal' => 'change_employee_no',
        ]);

        $this->dispatch('setEmployeeNo', employee_no: $employee_no);

    }

    public function render()
    {
        $query = EmployeeInformation::with('account', 'personal');

        // Filter by selected employment type
        if ($this->selectedType !== null) {
            if ($this->selectedType === 'unassigned') {
                $query->whereNull('employment_type_id')
                    ->where('isDeleted', false);
            } else if($this->selectedType === 'archived') {
                $query->where('isDeleted', true);
            } else {
                $query->where('employment_type_id', $this->selectedType)
                    ->where('isDeleted', false);
            }
        }

        // Search by employee number or full name
        if (!empty($this->search)) {
            $this->resetPage();

            $query->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        }

        $employees = $query->latest()->paginate($this->entries);

        return view('livewire.admin.hris.index', [
            'employees' => $employees
        ]);
    }


}
