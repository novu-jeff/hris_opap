<?php

namespace App\Livewire\Admin\Settings\Tranches;

use App\Models\Tranche;
use App\Models\TrancheItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use App\Models\EmployementTypes;

class Create extends Component
{

    use WithFileUploads;

    public $name;
    public $eligible;
    public $file;
    public $records;

    public $year;
    public $is_active;
    public $availableYears = [];

    public $employmentTypes = [];
    public $years = [];

    public function mount()
    {

        $startYear = 2018;
        $endYear   = now()->year + 1;

        $this->years = range($startYear, $endYear);

        $this->availableYears = Tranche::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

         // ✅ dynamic employment types
        $this->employmentTypes = EmployementTypes::orderBy('name')->get();   
    }

    public function updatedFile()
    {
        if ($this->file) {
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $extension = strtolower($file->getClientOriginalExtension());

                if ($extension === 'csv') {
                    try {
                        $files = Storage::files('public/temp/files');
                        Storage::delete($files);

                        $fileName = uniqid() . '.' . $extension;
                        $filePath = 'public/temp/files/' . $fileName;

                        $file->storeAs('public/temp/files', $fileName);

                        $this->parseCsv($filePath);

                    } catch (\Exception $e) {
                        return $this->dispatch('alert', [
                            'status' => 'warning',
                            'title' => 'Please be informed',
                            'isRemoveRowDT' => false,
                            'showAlert' => true,
                            'message' => 'An error occured: ' . $e->getMessage(),
                        ]);
                    }
                } else {
                     return $this->dispatch('alert', [
                            'status' => 'warning',
                            'title' => 'Please be informed',
                            'isRemoveRowDT' => false,
                            'showAlert' => true,
                            'message' => 'File must be CSV format!',
                        ]);
                }
            }

            $this->file = null;
        }
    }


    public function parseCsv($filePath)
    {
        $this->records = [];

        $expectedHeaders = [
            "salary_grade", "step_1", "step_1_wtax", "step_2", "step_2_wtax", "step_3", "step_3_wtax", "step_4", "step_4_wtax",
            "step_5", "step_5_wtax", "step_6", "step_6_wtax", "step_7", "step_7_wtax", "step_8", "step_8_wtax"
        ];

        if (!Storage::exists($filePath)) {
            return $this->dispatch('alert', [
                'status' => 'warning',
                'title' => 'Please be informed',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'File not found in storage!',
            ]);
        }

        $csv = Storage::get($filePath);
        $rows = array_map('str_getcsv', explode("\n", trim($csv)));

        $rows = array_filter($rows, fn($row) => !empty(array_filter($row)));

        if (!empty($rows)) {

            $headers = array_shift($rows);

            if ($headers !== $expectedHeaders) {
                return $this->dispatch('alert', [
                    'status' => 'warning',
                    'title' => 'Please be informed',
                    'isRemoveRowDT' => false,
                    'showAlert' => true,
                    'message' => 'Invalid CSV File!'
                ]);
            }

            $columnCount = count($headers);

            foreach ($rows as $row) {
                if (count($row) < $columnCount) {
                    $row = array_pad($row, $columnCount, "0");
                }

                if (count($row) === $columnCount) {
                    $this->records[] = array_combine($headers, $row);
                }
            }
        }

    }

    protected function rules() {
        return [
            'name' => 'required',
            'eligible' => 'required|exists:employment_types,id',
            'file' => empty($this->records) ? 'required' : 'nullable', 
    
        ];
    } 

    protected function messages() {
        return [
            'eligible.unique' => 'There\'s a current tranche already for this employment type'
        ];
    }

    public function save() {
        
        if (Gate::denies('write tranches')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }


       $this->validate();

        DB::beginTransaction();

        try {

            // Deactivate all other tranches for the same eligible type if this is active
            if ($this->is_active) {
                Tranche::where('eligible', $this->eligible)->update(['is_active' => 0]);
            }

            // Create or update the tranche
            $tranche = Tranche::updateOrCreate(
                [
                    'eligible' => $this->eligible,
                    'year'     => $this->year,
                ],
                [
                    'name'      => $this->name,
                    'is_active' => $this->is_active ? 1 : 0,
                ]
            );

            // Save tranche items...
            foreach ($this->records as $row) {
                $clean = fn($v) => ($v === "" || $v === "0") ? null : $v;

                $updateData = [];
                foreach (range(1, 8) as $i) {
                    $updateData["step_{$i}"]      = $clean($row["step_{$i}"] ?? null);
                    $updateData["step_{$i}_wtax"] = $clean($row["step_{$i}_wtax"] ?? null);
                }

                TrancheItems::updateOrCreate(
                    [
                        'tranche_id'   => $tranche->id,
                        'salary_grade' => $row['salary_grade'],
                    ],
                    $updateData
                );
            }

            DB::commit();

            $this->dispatch('alert', [
                'status'    => 'success',
                'title'     => 'Success!',
                'showAlert' => true,
                'message'   => 'Tranche saved successfully.'
            ]);

            $this->reset();

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'status'    => 'error',
                'title'     => 'Error',
                'showAlert' => true,
                'message'   => $e->getMessage()
            ]);
        }
}
    
    public function render()
    {

        return view('livewire.admin.settings.tranches.create');
    }
}
