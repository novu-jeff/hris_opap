<?php

namespace App\Livewire\Admin\Hris\Profile;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Models\EmployeeEducation;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Education extends Component
{

    use WithFileUploads;

    public $employee_id;
    public $employee_no;
    public $originalData;
    public $recordIndex;
    public $records;

    protected $listeners = ['save', 'removeRecord', 'removeDocument'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $data = EmployeeEducation::where('employee_no', $this->employee_no)
            ->get()
            ->toArray() ?? [];

        $mappedData = array_map(function ($item) {
            if (!is_null($item['documents'])) {
                $item['document_control'] = true;
            } else {
                $item['document_control'] = false;
            }
            return $item;
        }, $data);

        $this->originalData = $data;
        $this->records = $mappedData;
    }

    public function addRecord() {
        if (isset($this->defaultFields)) {
            $this->records[] = $this->defaultFields;
        }
    }

    public function removeRecord(bool $isNotify, $index = null) {
        
        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you will be deleting this record and cannot be undone.';
            $action = 'removeRecord';
            $this->recordIndex = $index;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

            return;
        }

        $records = EmployeeEducation::where('employee_no', $this->employee_no)
            ->orderBy('created_at', 'asc')
            ->get();

        $record = $records[$this->recordIndex] ?? null;

        if ($record || $record->documents) {
            $path = 'documents/' . $this->employee_no . '/' . $record->documents;

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $record->delete();

        }

        unset($this->records[$this->recordIndex]);
        $this->records = array_values($this->records);
        
    }

    public function removeDocument(bool $isNotify, $index = null)
    {
        if ($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you will be removing the document only.';
            $action = 'removeDocument';
            $this->recordIndex = $index;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

            return;
        }

        $records = EmployeeEducation::where('employee_no', $this->employee_no)
            ->orderBy('created_at', 'asc')
            ->get();

        $record = $records[$this->recordIndex] ?? null;

        if ($record || $record->documents) {
            $path = 'documents/' . $this->employee_no . '/' . $record->documents;

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $record->documents = null;
            $record->save();

            $this->records[$this->recordIndex]['documents'] = null;
        }

        $this->loadRecords();

        return $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Success!',
            'isRemoveRowDT' => false,
            'isReloadDT' => false,
            'message' => 'Document has been successfully removed.',
            'redirect' => '_stay'
        ]);
    }


    private $defaultFields = [
        'level' => '',
        'school_name' => '',
        'course' => '',
        'from_year' => '',
        'to_year' => '',
        'documents' => '',
        'highest_level' => '',
        'year_graduated' => '',
        'scholarship_honors' => '',
        'document_control' => false
    ];

    protected function rules(?string $employee_no = null) {
        return [
            'records.*.level' => 'required|string',
            'records.*.school_name' => 'required|string|max:255',
            'records.*.course' => 'required|string|max:255',
            'records.*.from_year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'records.*.to_year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'records.*.highest_level' => 'nullable|string',
            'records.*.year_graduated' => 'nullable|numeric',
            'records.*.scholarship_honors' => 'nullable',
            'records.*.documents' => 'nullable|mimes:jpg,png,jpeg,pdf',
            'records.*.documents' => function ($attribute, $value, $fail) {
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
            'records.*.level.required' => '* required',
            'records.*.school_name.required' => '* required',
            'records.*.course.required' => '* required',
            'records.*.from_year.required' => '* required',
            'records.*.from_year.digits' => '* must start atleast 1900.',
            'records.*.from_year.integer' => '* must be numeric.',
            'records.*.from_year.min' => '* too early.',
            'records.*.from_year.max' => '* cannot be in the future.',

            'records.*.to_year.required' => '* required',
            'records.*.to_year.digits' => '* must start atleast 1900.',
            'records.*.to_year.integer' => '* must be numeric.',
            'records.*.to_year.min' => '* too early.',
            'records.*.to_year.max' => '* cannot be in the future.',
            'records.*.to_year.after_or_equal' => '* must be the same or after the start year.',
        ];
    }

    public function hasChanges()
    {
        $originalData = $this->originalData ?? [];
        $records = $this->records;

        foreach ($records as $key => $newValue) {
            if (!array_key_exists($key, $originalData)) {
                return true;
            }

            $oldValue = $originalData[$key];

            if ($newValue !== $oldValue) {
                return true;
            }
        }

        return false;
    }

    public function setErrorActiveTabAccordions(array $errorKeys) {
        $this->dispatch('scrollToError', $errorKeys);
    }

    public function download(int $index) {
        
        $files = EmployeeEducation::where('employee_no', $this->employee_no)
            ->orderBy('created_at', 'asc')
            ->pluck('documents');

        $file = $files[$index] ?? null;

        $path = 'documents/' . $this->employee_no . '/' . $file;

        if ($file && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        return $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Oops',
            'isRemoveRowDT' => false,
            'showAlert' => true,
            'message' => 'The file you\'re trying to download could not be located. It may have been moved, renamed, or deleted from the server. Please verify that the file still exists or contact the administrator for further assistance.'
        ]);
    }

    public function save(bool $isNotify = true) {

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

        try {
            $this->validate($this->rules());
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->keys();
            $this->setErrorActiveTabAccordions($errors);
            $this->dispatch('scrollToError', $errors);
            throw $e;
        }

        if(!$this->hasChanges()) {
            return $this->dispatch('alert', [
                'status' => 'info',
                'title' => 'Please be informed!',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Unable to save because no changes were made, feel free to edit or update your informations first before saving. Thank you!'
            ]);
        }

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

            return;
        }

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

        DB::beginTransaction();

        try {

            $process = new HRISProcessingService;
            $process->save( false, $id, $id, 'education', $this->records);

            DB::commit();

            $this->loadRecords();

            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee ' . strtoupper($id) . ' records saved successfully.',
                'redirect' => '_stay'
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

    public function render()
    {
        return view('livewire.admin.hris.profile.education');
    }
}

