<?php

namespace App\Livewire\Admin\Hris\Profile;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Models\EmployeeInformation;
use App\Models\EmployeeParents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Family extends Component
{
    public $employee_id;
    public $employee_no;

    public $originalData;
    public $recordIndex;
    public $records;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $data = EmployeeParents::where('employee_no', $this->employee_no)->first();
        $this->originalData = $data;
        $this->records = $this->formatRecords($data);
    }

    protected function formatRecords($data) {
        return [
            'spouse_surname' => $data->spouse_surname ?? null,
            'spouse_firstname' => $data->spouse_firstname ?? null,
            'spouse_middlename' => $data->spouse_middlename ?? null,
            'spouse_suffix' => $data->spouse_suffix ?? null,
            'spouse_occupation' => $data->spouse_occupation ?? null,
            'spouse_business_name_employer' => $data->spouse_business_name_employer ?? null,
            'spouse_business_address' => $data->spouse_business_address ?? null,
            'spouse_contact_no' => $data->spouse_contact_no ?? null,
            'father_surname' => $data->father_surname ?? null,
            'father_firstname' => $data->father_firstname ?? null,
            'father_middlename' => $data->father_middlename ?? null,
            'father_suffix' => $data->suffix ?? null,
            'mother_surname' => $data->mother_surname ?? null,
            'mother_firstname' => $data->mother_firstname ?? null,
            'mother_middlename' => $data->mother_middlename ?? null,
        ];
    }

    protected function rules() {
        return [
            'records.spouse_surname' => 'nullable|string|max:255',
            'records.spouse_firstname' => 'nullable|string|max:255',
            'records.spouse_middlename' => 'nullable|string|max:255',
            'records.spouse_suffix' => 'nullable|string|max:10',
            'records.spouse_occupation' => 'nullable|string|max:255',
            'records.spouse_business_name_employer' => 'nullable|string|max:255',
            'records.spouse_business_address' => 'nullable|string|max:255',
            'records.spouse_contact_no' => 'nullable|string|max:20',
            
            'records.father_surname' => 'nullable|string|max:255',
            'records.father_firstname' => 'nullable|string|max:255',
            'records.father_middlename' => 'nullable|string|max:255',
            'records.father_suffix' => 'nullable|string|max:10',

            'records.mother_surname' => 'nullable|string|max:255',
            'records.mother_firstname' => 'nullable|string|max:255',
            'records.mother_middlename' => 'nullable|string|max:255',
        ];
    }

    protected function messages() {
        return [
            'records.spouse_surname.string' => 'Spouse surname must be a valid string.',
            'records.spouse_firstname.string' => 'Spouse first name must be a valid string.',
            'records.spouse_middlename.string' => 'Spouse middle name must be a valid string.',
            'records.spouse_suffix.string' => 'Spouse suffix must be a valid string.',
            'records.spouse_suffix.max' => 'Spouse suffix must not exceed 10 characters.',
            'records.spouse_occupation.string' => 'Spouse occupation must be a valid string.',
            'records.spouse_business_name_employer.string' => 'Spouse employer/business name must be a valid string.',
            'records.spouse_business_address.string' => 'Spouse business address must be a valid string.',
            'records.spouse_contact_no.string' => 'Spouse contact number must be a valid string.',

            'records.father_surname.string' => 'Father\'s surname must be a valid string.',
            'records.father_firstname.string' => 'Father\'s first name must be a valid string.',
            'records.father_middlename.string' => 'Father\'s middle name must be a valid string.',
            'records.father_suffix.string' => 'Father\'s suffix must be a valid string.',
            'records.father_suffix.max' => 'Father\'s suffix must not exceed 10 characters.',

            'records.mother_surname.string' => 'Mother\'s surname must be a valid string.',
            'records.mother_firstname.string' => 'Mother\'s first name must be a valid string.',
            'records.mother_middlename.string' => 'Mother\'s middle name must be a valid string.',
        ];
    }

    public function hasChanges()
    {
        $originalData = $this->originalData?->toArray() ?? [];
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
            $process->save(false, $id, $id, 'family', $this->records);

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
        return view('livewire.admin.hris.profile.family');
    }
}
