<?php

namespace App\Livewire\Admin\Hris\Profile;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use Cache;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Personal extends Component
{
    public $employee_id;
    public $employee_no;
    public array $countries;
    public bool $isDualCitizenship = false;
    public bool $isFromUpdate = false;
    public bool $isMarried = false;
    public bool $hasBirthCert = false;
    public bool $hasMarriageCert = false;
    public $originalData;
    public $records;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
        $this->loadCountries();
    }

    public function loadRecords() {
        $data = EmployeePersonal::where('employee_no', $this->employee_no)->first();
        $this->originalData = $data;
        $this->records = $this->formatRecords($data);
    }

    public function loadCountries() {

        if (Cache::has('countries')) {
            return $this->countries = Cache::get('countries');
        }

        try {
            $client = new Client();
            $response = $client->get('https://restcountries.com/v3.1/all?fields=name');
            $countries = json_decode($response->getBody(), true);

            if (!is_array($countries)) {
                throw new \Exception('Invalid API response');
            }

            usort($countries, fn($a, $b) => strcmp($a['name']['common'], $b['name']['common']));

            Cache::put('countries', $countries, now()->addHours(24));

            $this->countries = $countries;
            return $this->countries;
        } catch (\Exception $e) {
            logger()->error('Failed to load countries: ' . $e->getMessage());
            return $this->countries = [];
        }
    }

    public function select_change(string $property) {

        if($property == 'citizenship') {
            if($this->records['citizenship'] == 'dual_citizenship') {
                $this->isDualCitizenship = true;
            } else {
                $this->isDualCitizenship = false;
            }
        }

        if($property == 'civil_status') {
            if($this->records['civil_status'] == 'married') {
                $this->isMarried = true;
            } else {
                $this->isMarried = false;
            }
        }
    }

    protected function formatRecords($data) {


        if(!empty($data->birth_certificate)) {
            $this->hasBirthCert = true;
        } 

        if(!empty($data->marriage_certificate)) {
            $this->hasMarriageCert = true;
        } 
    
        $email = $data->email 
            ?? ($data->account['email'] ?? null);
    
        $fields = [
            'profile', 'firstname', 'middlename', 'lastname', 'suffix', 'birthday',
            'civil_status', 'sex', 'citizenship', 'citizenship_type', 'country',
            'present_address', 'present_province', 'present_city', 'permanent_address',
            'permanent_province', 'permanent_city', 'mobile_number', 'tel_no', 'height',
            'weight', 'blood_type', 'gsis_no', 'pagibig_no', 'philhealth_no', 'sss_no',
            'tin_no', 'email' 
        ];
    
        $formattedPersonal = array_combine(
            $fields, 
            array_map(fn($field) => $field === 'email' ? $email : ($data[$field] ?? null), $fields)
        );

    
        return $formattedPersonal;
    }

    protected function rules(?string $employee_no = null) {
        return [
            'records.firstname' => 'required|string|max:255',
            'records.lastname' => 'required|string|max:255',
            'records.suffix' => 'nullable|in:jr,sr,I,II,III,IV,V',
            'records.civil_status' => 'nullable|in:single,married,divorced,seperated,widowed,anulled',
            'records.sex' => 'nullable|in:male,female',
            'records.citizenship_type' => 'nullable|required_with:records.citizenship',
            'records.country' => 'required_if:records.citizenship,dual_citizenship',

            'records.mobile_number' => 'nullable|regex:/^09\d{9}$/',
            'records.email' => [
                'required',
                Rule::unique('employee_account', 'email')->ignore($employee_no, 'employee_no')
            ],
        ];
    }

    protected function messages() {
        return [
           
            'records.firstname.required' => 'The first name is required.',
            'records.lastname.required' => 'The last name is required.',
            'records.suffix.in' => 'The suffix must be one of the following: jr, sr, I, II, III, IV, or V.',
            'records.civil_status.in' => 'The civil status must be one of the following: single, married, divorced, separated, widowed, or annulled.',
            'records.sex.in' => 'The sex must be either male or female.',
            'records.citizenship_type.required_with' => 'The citizenship type is required when citizenship is provided.',
            'records.country.required_if' => 'The country is required when citizenship is dual citizenship.',
            'records.mobile_number.regex' => 'The mobile number format is invalid. It should start with 09 and be followed by 9 digits.',
            'records.email.email' => 'The email must be a valid email address.',
            'records.email.required' => 'The email is required.',
            'records.email.unique' => 'The email is already taken.',
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
            $this->validate($this->rules($id));
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
            $process->save(false, $id, $id, 'personal', $this->records);

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
        return view('livewire.admin.hris.profile.personal');
    }
}

