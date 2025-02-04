<?php

namespace App\Livewire\Admin\Settings\Hris\Position;

use App\Models\Positions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{


    use WithFileUploads;

    public $file;
    public $records;

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
            "Position", "Salary Grade",
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
            'file' => empty($this->records) ? 'required' : 'nullable', 
    
        ];
    } 

    public function save() {
        
        if (Gate::denies('write positions')) {
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

            $records = [];

            foreach ($this->records as $record) {
                $records[] = [
                    'name' => $record['Position'],          
                    'salary_grade' => $record['Salary Grade'] 
                ];
            }
            
            foreach ($records as $record) {
                Positions::updateOrCreate(
                    ['name' => $record['name']], 
                    ['salary_grade' => $record['salary_grade']] 
                );
            }
            

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Positions has been added successfully.'
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
        return view('livewire.admin.settings.hris.position.create');
    }
}
