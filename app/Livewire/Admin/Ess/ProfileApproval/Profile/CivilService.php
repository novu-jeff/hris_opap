<?php

namespace App\Livewire\Admin\Ess\ProfileApproval\Profile;

use App\Models\EmployeeCivilService;
use App\Models\EmployeeUpdateCivilService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CivilService extends Component
{
    public $employee_no;
    public $originalData;
    public $records;
    public function mount() {
        $this->loadRecords();
    }

    public function download(int $index) {
        
        $files = EmployeeUpdateCivilService::where('employee_no', $this->employee_no)
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

    public function loadRecords()
    {
        $updated = EmployeeUpdateCivilService::where('employee_no', $this->employee_no)->get();
        $stored = EmployeeCivilService::where('employee_no', $this->employee_no)->get();

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
            'certification' => $data['certification'] ?? null,
            'rating' => $data['rating'] ?? null,
            'date_exam' => $data['date_exam'] ?? null,
            'place_exam' => $data['place_exam'] ?? null,
            'license_no' => $data['license_no'] ?? null,
            'date_validity' => $data['date_validity'] ?? null,
        ];
    }
    
    public function render()
    {
        return view('livewire.admin.ess.profile-approval.profile.civil-service');
    }
}
