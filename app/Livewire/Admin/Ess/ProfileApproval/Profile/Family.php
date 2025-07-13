<?php

namespace App\Livewire\Admin\Ess\ProfileApproval\Profile;

use App\Models\EmployeeParents;
use App\Models\EmployeeUpdateParents;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Family extends Component
{
    public $employee_no;
    public $originalData;
    public $records;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $updated = EmployeeUpdateParents::where('employee_no', $this->employee_no)->first();
        $stored = EmployeeParents::where('employee_no', $this->employee_no)->first();

        $data = $updated ?? $stored;

        if(!is_null($stored)) {
            $fields = array_keys($stored->getAttributes());
            $this->records = $this->compareFields($stored, $updated, $fields);
        } else {
            $fields = array_keys($this->formatRecords($data));
            $this->records = $this->compareFields($stored, $updated, $fields);
        }
    }

   
    private function compareFields($oldRecord, $newRecord, $fields)
    {
        $result = [];
        foreach ($fields as $field) {
            $oldValue = $oldRecord ? $oldRecord->$field : '';
            $newValue = $newRecord ? $newRecord->$field : '';
    
            $result[$field] = [
                'old' => $oldValue,
                'new' => (empty($newValue) || is_null($newValue)) ? $oldValue : $newValue,
            ];
        }

        return $result;
    }

    protected function formatRecords($data) {
        return [
            'spouse_surname' => $data['spouse_surname'] ?? null,
            'spouse_firstname' => $data['spouse_firstname'] ?? null,
            'spouse_middlename' => $data['spouse_middlename'] ?? null,
            'spouse_suffix' => $data['spouse_suffix'] ?? null,
            'spouse_occupation' => $data['spouse_occupation'] ?? null,
            'spouse_business_name_employer' => $data['spouse_business_name_employer'] ?? null,
            'spouse_business_address' => $data['spouse_business_address'] ?? null,
            'spouse_contact_no' => $data['spouse_contact_no'] ?? null,
            'father_surname' => $data['father_surname'] ?? null,
            'father_firstname' => $data['father_firstname'] ?? null,
            'father_middlename' => $data['father_middlename'] ?? null,
            'father_suffix' => $data['father_suffix'] ?? null,
            'mother_surname' => $data['mother_surname'] ?? null,
            'mother_firstname' => $data['mother_firstname'] ?? null,
            'mother_middlename' => $data['mother_middlename'] ?? null,
        ];
    }
    
    public function download(int $index) {
        
        $files = EmployeeUpdateParents::where('employee_no', $this->employee_no)
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

    public function render()
    {
        return view('livewire.admin.ess.profile-approval.profile.family');
    }
}
