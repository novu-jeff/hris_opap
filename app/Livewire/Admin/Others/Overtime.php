<?php

namespace App\Livewire\Admin\Others;

use App\Jobs\Parallel\Overtime as ParallelOvertime;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Bus\Batch;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Overtime extends Component
{
    use WithFileUploads;

    public $file;
    public $upload_preview;
    public $cut_off_period;
    public $tempPath;
    public $monthYear;
    public bool $isParsing = false, $isUploading = false;
    public $batch_id;
    public $actionBy;

    protected $listeners = ['cancelUpload'];

    protected $rules = [
        'cut_off_period' => 'required',
        'upload_preview' => 'required|string',
    ];

    public function mount()
    {
        $this->actionBy = Auth::user();
    }

    public function updatedFile()
    {
        if (!$this->file) return;

        $file = $this->file;

        if (!in_array($file->getClientOriginalExtension(), ['csv', 'xls', 'xlsx'])) {
            $this->addError('file', 'The file must be in CSV, XLS, or XLSX format.');
            return;
        }

        try {
            Storage::delete(Storage::allFiles('public/temp/files'));

            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/temp/files', $fileName);
            $this->upload_preview = asset('storage/temp/files/' . $fileName);

            $spreadsheet = IOFactory::load(storage_path("app/$filePath"));
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            if (empty($data)) {
                $this->addError('file', 'File is empty.');
                return;
            }

            $headers = array_map('trim', array_map('strtoupper', $data[0]));
            $this->checkIfValidFormat($headers);

            $chunks = array_chunk($data, 1000);

            $tempPath = resource_path('temp/' . time());
            if (!file_exists($tempPath)) mkdir($tempPath, 0777, true);
            $this->tempPath = $tempPath;

            foreach ($chunks as $index => $chunkData) {
                $chunkFileName = "/tmp_{$index}.csv";
                $chunkFilePath = $tempPath . $chunkFileName;

                $handle = fopen($chunkFilePath, 'w');
                foreach ($chunkData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            }

            $this->isParsing = false;

        } catch (\Exception $e) {
            $this->addError('file', 'Error: ' . $e->getMessage());
            $this->isParsing = false;
        }

        $this->file = null;
    }

    public function upload_file()
    {
        $this->validate();

        try {
            $path = $this->tempPath;
            $files = glob($path . '/*.csv');

            $jobs = [];

            foreach ($files as $file) {
                $data = array_map('str_getcsv', file($file));
                array_shift($data);
                $jobs[] = new ParallelOvertime($this->cut_off_period, $data);
                unlink($file);
            }

            if (!empty($jobs)) {
                Bus::batch($jobs)
                    ->withOption('actionBy', [
                        'id' => $this->actionBy->id,
                        'name' => $this->actionBy->name
                    ])
                    ->name('Parallel Overtime Upload Batch')
                    ->catch(function (Batch $batch, \Throwable $e) {
                        \Log::error('Error: ' . $e->getMessage());
                        $this->actionBy?->notify(new Notifications(
                            'error',
                            'Parallel Overtime for testing failed.',
                            route('system.jobs', ['id' => $batch->id]),
                            'admin'
                        ));
                    })
                    ->then(function (Batch $batch) {
                        $this->actionBy?->notify(new Notifications(
                            'success',
                            'Parallel Overtime for testing completed.',
                            route('system.jobs', ['id' => $batch->id]),
                            'admin'
                        ));
                    })
                    ->dispatch();
            }

            $this->dispatch('alert', [
                'status' => 'info',
                'title' => 'Upload Started',
                'showAlert' => true,
                'message' => 'Overtime logs are being processed. You’ll be notified when it finishes.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Upload Error',
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isUploading = false;
        }
    }

    private function checkIfValidFormat($headers)
    {
        $requiredHeaders = [
            'EMPLOYEE ID',
            'NAME',
            'TOTAL OVERTIME',
            'TOTAL OVERTIME HRS',
            'TOTAL NIGHDIFF AMOUNT',
            'TOTAL NIGHDIFF HRS',
        ];

        $missing = array_diff($requiredHeaders, $headers);

        if (!empty($missing)) {
            throw new \Exception('Missing required columns: ' . implode(', ', $missing));
        }
    }

    public function render()
    {
        return view('livewire.admin.others.overtime');
    }
}
