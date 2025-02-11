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
            "salary_grade", "step_1", "step_2", "step_3", "step_4",
            "step_5", "step_6", "step_7", "step_8"
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

        try {
           
            Tranche::where('id', $this->id)->update([
                'name' => $this->name,
                'eligible' => $this->eligible
            ]);            

            TrancheItems::where('tranche_id', $this->id)
                ->delete();

            foreach ($this->records as $key => $data) {
                $this->records[$key]['tranche_id'] = $this->id;
            }

            TrancheItems::insert($this->records);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Tranche was updated successfully.'
            ]);

        } catch (\Exception $e) {
            
            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'showAlert' => true,
                'message' => 'Error occured: ' . $e->getMessage()
            ]);

        }

    }

    public function render()
    {
        return view('livewire.admin.settings.tranches.edit');
    }
}
