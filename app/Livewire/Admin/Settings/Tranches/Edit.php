<?php

namespace App\Livewire\Admin\Settings\Tranches;

use App\Models\Tranche;
use App\Models\TrancheItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    use WithFileUploads;

    public $id;
    public $name;
    public $eligible;
    public $file;
    public $records;

    public $year;
    public $is_active;
    public $availableYears = [];

    public function mount() {
        

        $this->availableYears = Tranche::select('year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->toArray();

       $records = $this->loadRecords();

       // $this->year = null; // <--- Important, initially empty
        $this->year = $records->year;
    }

    public function loadRecords() {
        // $records = Tranche::with('items')->where('id', $this->id)
        //     ->first();
        $records = Tranche::with('items')->where('id', $this->id)->firstOrFail();

        $this->name = $records->name;
        $this->eligible = $records->eligible;
        $this->year = $records->year;
        $this->is_active = (bool) $records->is_active;
        $this->records = $records->items->toArray() ?? [];

         return $records; // <--- return it if needed
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
            'year'     => [
            'required',
            'integer',
            Rule::unique('tranche')
                ->where(fn ($q) => $q->where('eligible', $this->eligible))
                ->ignore($this->id), // ✅ ignore current record
        ],
            'file'     => 'nullable|file|mimes:csv,txt', // <--- optional file
    
        ];
    } 


    protected function messages() {
        return [
            'year.unique' => 'A tranche for this eligible already exists for the selected year.',
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

             // Deactivate all other tranches for the same eligible type if this is active
            // if ($this->is_active) {
                 Tranche::where('eligible', $this->eligible)->update(['is_active' => 0]);
            // }

             // deactivate other tranches for the same year
            // Tranche::where('year', $this->year)->update([
            //     'is_active' => false
            // ]);

            // create or update tranche
            // $tranche = Tranche::updateOrCreate(
            //     ['id' => $this->tranche_id ?? null],
            //     [
            //         'name'      => $this->name,
            //         'year'      => $this->year,
            //         'eligible'  => $this->eligible,
            //         'is_active' => true,
            //     ]
            // );


//dd($this->id);
            // Update tranche basic info
            $tranche = Tranche::findOrFail($this->id);
            $tranche->name = $this->name;
            $tranche->eligible = $this->eligible;
            $tranche->is_active = $this->is_active ? 1 : 0;
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

    public function updatedYear($year)
    {
        
        if (!$year) {
            $this->records = [];
            return;
        }

        // Get the first tranche for the selected year, active or inactive
        $tranche = Tranche::where('year', $year)->first();

        if ($tranche) {
            // If tranche exists, load its items
            $this->records = TrancheItems::where('tranche_id', $tranche->id)
                ->orderBy('salary_grade')
                ->get()
                ->toArray();
        } else {
            // No tranche exists yet for this year → empty records
            $this->records = [];
        }
   
    }


    public function render()
    {
        return view('livewire.admin.settings.tranches.edit');
    }
}
