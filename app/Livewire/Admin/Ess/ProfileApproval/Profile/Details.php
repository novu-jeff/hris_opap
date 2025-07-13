<?php

namespace App\Livewire\Admin\Ess\ProfileApproval\Profile;

use App\Models\EmployeePersonal;
use App\Models\EmployeeUpdatePersonal;

use Livewire\Component;

class Details extends Component
{
    public $employee_no;
    public $records;
    public $isDualCitizenship;

    public function mount() {
        $this->loadRecords();
    }

    protected $listeners = ['redirect'];

    public function loadRecords() {

        $updated = EmployeeUpdatePersonal::where('employee_no', $this->employee_no)->first();
        $stored = EmployeePersonal::where('employee_no', $this->employee_no)->first();

        $fields = array_keys($stored->getAttributes());

        $this->records = $this->compareFields($stored, $updated, $fields);
    }

   
    private function compareFields($oldRecord, $newRecord, $fields)
    {
        $result = [];
        foreach ($fields as $field) {
            // Use null coalescing operator to ensure we don't get null if data is missing
            $oldValue = $oldRecord ? $oldRecord->$field : '';
            $newValue = $newRecord ? $newRecord->$field : '';
    
            // Store the results of comparison
            $result[$field] = [
                'old' => $oldValue,
                'new' => (empty($newValue) || is_null($newValue)) ? $oldValue : $newValue,
            ];
        }

        return $result;
    }

    protected function formatRecords($data) {
        return [
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
        ];
    }

    public function render()
    {
        return view('livewire.admin.ess.profile-approval.profile.details');
    }
}
