<?php

namespace App\Livewire\Admin\Ess\ProfileApproval\Profile;

use App\Models\EmployeeEmploymentHistory;
use App\Models\EmployeeUpdateEmploymentHistory;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Employment extends Component
{
    public $employee_no;
    public $originalData;
    public $records;
    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $updated = EmployeeUpdateEmploymentHistory::where('employee_no', $this->employee_no)->get();
        $stored = EmployeeEmploymentHistory::where('employee_no', $this->employee_no)->get();

        $reference = $stored->first() ?? $updated->first();

        if (is_null($reference)) {
            $this->records = [];
            return;
        }

        $fields = array_keys($reference->getAttributes());

        $this->records = [];

        $max = max($stored->count(), $updated->count());
        for ($i = 0; $i < $max; $i++) {
            $storedRecord = $stored[$i] ?? null;
            $updatedRecord = $updated[$i] ?? null;

            $this->records[] = $this->compareFields($storedRecord, $updatedRecord, $fields);
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
            'position' => $data['position'] ?? null,
            'department' => $data['department'] ?? null,
            'monthly_salary' => $data['monthly_salary'] ?? null,
            'salary_pay_grade' => $data['salary_pay_grade'] ?? null,
            'employment_status' => $data['employment_status'] ?? null,
            'isGovernment' => $data['isGovernment'] ?? null,
            'from_year' => $data['from_year'] ?? null,
            'to_year' => $data['to_year'] ?? null,
        ];
    }
    
    public function download(int $index) {
        
        $files = EmployeeUpdateEmploymentHistory::where('employee_no', $this->employee_no)
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
        return view('livewire.admin.ess.profile-approval.profile.employment');
    }
}
