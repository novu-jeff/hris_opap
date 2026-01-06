<?php

namespace App\Livewire\Admin\Settings\Tranches;

use App\Models\Tranche;
use App\Models\TrancheItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $id;
    public $name;
    public $eligible;
    public $file;
    public $records;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $records = Tranche::with('items')->where('id', $this->id)
            ->first();
        $this->name = $records->name;
        $this->eligible = $records->eligible;
        $this->records = $records->items->toArray() ?? [];
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

    public function save() {
        

       /* if (Gate::denies('write tranches')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }*/


        $this->validate();

        DB::beginTransaction();

        try {
            // Update tranche basic info
            $tranche = Tranche::findOrFail($this->id);
            $tranche->name = $this->name;
            $tranche->eligible = $this->eligible;
            $tranche->save();

            // We will re-sync tranche items (safe for edit)
            TrancheItems::where('tranche_id', $this->id)->delete();

            foreach ($this->records as $row) {

                // Helper to clean CSV values
                $clean = fn ($v) => ($v === '' || $v === '0') ? null : $v;

                $insertData = [
                    'tranche_id'   => $this->id,
                    'salary_grade' => $row['salary_grade'],
                ];

                // Handle step + step_wtax dynamically
                foreach (range(1, 8) as $i) {
                    $insertData["step_{$i}"]       = $clean($row["step_{$i}"] ?? null);
                    $insertData["step_{$i}_wtax"]  = $clean($row["step_{$i}_wtax"] ?? null);
                }

                TrancheItems::create($insertData);
            }

            DB::commit();

            $this->dispatch('alert', [
                'status'    => 'success',
                'title'     => 'Success!',
                'showAlert' => true,
                'message'   => 'Tranche steps and withholding tax updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'status'    => 'error',
                'title'     => 'Oops!',
                'showAlert' => true,
                'message'   => 'Error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.tranches.edit');
    }
}
