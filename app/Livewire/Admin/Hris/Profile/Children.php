<?php

namespace App\Livewire\Admin\Hris\Profile;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Models\EmployeeChildren;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Children extends Component
{

    use WithFileUploads;
    
    public $employee_id;
    public $employee_no;
    public $originalData;
    public $recordIndex;
    public $records;

    protected $listeners = ['save', 'removeRecord'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $data = EmployeeChildren::where('employee_no', $this->employee_no)
            ->get()
            ->toArray() ?? [];

        $this->originalData = $data;
        $this->records = $data;
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

        $records = EmployeeChildren::where('employee_no', $this->employee_no)
            ->orderBy('created_at', 'asc')
            ->get();

        $record = $records[$this->recordIndex] ?? null;

        if ($record && $record->documents) {
            $path = 'documents/' . $this->employee_no . '/' . $record->documents;

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

        }

        $record->delete();

        unset($this->records[$this->recordIndex]);
        $this->records = array_values($this->records);
        
    }

    private $defaultFields = [
        'firstname' => '',
        'middlename' => '',
        'lastname' => '',
        'birthdate' => '',
        'documents' => '',
    ];

    protected function rules(?string $employee_no = null) {
        return [
            'records.*.firstname' => 'required|string|max:255',
            'records.*.middlename' => 'nullable|string|max:255',
            'records.*.lastname' => 'required|string|max:255',
            'records.*.birthdate' => 'required|date',
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
            'records.*.firstname.required' => 'Each child must have a first name.',
            'records.*.middlename.string' => 'The middle name must be a string.',
            'records.*.lastname.required' => 'Each child must have a last name.',
            'records.*.birthdate.required' => 'The birthdate is required for each child.',
            'records.*.birthdate.date' => 'The birthdate must be a valid date.',
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

    private function uploadFile($employee_no, $identifier, $path, $file)
    {
        try {
            if ($file instanceof TemporaryUploadedFile) {
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
        } catch (\Exception $e) {
            \Log::error('File upload failed', [
                'employee_no' => $employee_no,
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }


    public function download(int $index) {
        
        $files = EmployeeChildren::where('employee_no', $this->employee_no)
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
            $process->save(false, $id, $id, 'children', $this->records);

            DB::commit();

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
        return view('livewire.admin.hris.profile.children');
    }
}
