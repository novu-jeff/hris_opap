<?php

namespace App\Livewire\Admin\Ess\ProfileApproval\Profile;

use App\Models\EmployeePersonal;
use App\Models\EmployeeUpdatePersonal;
use App\Models\EmployeeAccount;
use Illuminate\Support\Facades\Log;

use Livewire\Component;

class Personal extends Component
{
    public $employee_no;
    public $records;
    public $isDualCitizenship;

    public function mount($employee_no) {
         $this->employee_no = $employee_no;
        $this->loadRecords();
    }

    protected $listeners = ['redirect'];

    public function loadRecords() {

         if (!$this->employee_no) {
            $this->records = [];
            return;
        }


        $updated = EmployeeUpdatePersonal::where('employee_no', $this->employee_no)->first();
        $stored = EmployeePersonal::where('employee_no', $this->employee_no)->first();
        $account = EmployeeAccount::where('employee_no', $this->employee_no)->first(); 

        if (!$stored) {
            $this->records = [];
            return;
        }

       // $fields = array_keys($stored->getAttributes());
          $fields = (new EmployeePersonal)->getFillable();
          $fields[] = 'email'; // include email explicitly

        $this->records = $this->compareFields($stored, $updated, $fields, $account);
    }



    private function compareFields($oldRecord, $newRecord, $fields, $account = null)
        {
            $result = [];
            foreach ($fields as $field) {

                // Use EmployeeAccount for email
                if ($field === 'email') {
                    $oldValue = $account ? $account->email ?? '' : '';
                } else {
                    $oldValue = $oldRecord ? $oldRecord->$field ?? '' : '';
                }

                $updatedValue = $newRecord ? $newRecord->$field ?? '' : '';

                $oldValue = trim($oldValue);
                $updatedValue = trim($updatedValue);

                $result[$field] = [
                    'old' => $oldValue,
                    'new' => $updatedValue,
                    'changed' => $updatedValue !== '' && $updatedValue != $oldValue
                ];
            }

            \Log::info('Full personal field comparison', [
                'employee_no' => $this->employee_no,
                'comparison' => $result,
            ]);

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
        return view('livewire.admin.ess.profile-approval.profile.personal');
    }
}
