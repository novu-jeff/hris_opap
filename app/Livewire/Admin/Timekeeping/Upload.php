<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Jobs\TimelogUploadProcess;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Bus\Batch;
use Carbon\Carbon;

class Upload extends Component
{

    use WithFileUploads;

    public $file;
    public $upload_preview;
    public $tempPath;
    public $batchInfo;
    public $monthYear;
    public bool $isParsing, $isUploading = false;
    public $isLoading = false;
    public $batch_id;
    public $actionBy;

    protected $listeners = ['cancelUpload'];

    public function mount() {
        $this->actionBy = Auth::user();
    }

 
    public function updatedFile()
{
    if (!$this->file) {
        $this->isParsing = true;
        return;
    }

    $file = $this->file;

    if (!($file instanceof \Illuminate\Http\UploadedFile) || $file->getClientOriginalExtension() !== 'csv') {
        $this->addError('file', 'The file must be in CSV format.');
        $this->file = null;
        return;
    }

    try {
        // Clear old temp files
        Storage::delete(Storage::allFiles('public/temp/files'));

        // Store the uploaded CSV
        $fileName = uniqid() . '.csv';
        $filePath = $file->storeAs('public/temp/files', $fileName);
        $this->upload_preview = asset('storage/temp/files/' . $fileName);

        // Prepare temp folder for chunks
        $tempPath = resource_path('temp/' . time());
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }
        $this->tempPath = $tempPath;

        // Read CSV using fgetcsv (robust for Excel CSVs)
        $handle = fopen(storage_path("app/$filePath"), 'r');
        if (!$handle) {
            throw new \Exception("Unable to open uploaded CSV file.");
        }

        $header = fgetcsv($handle);
        if (!$header) {
            throw new \Exception("CSV file is empty or malformed.");
        }

        // Remove BOM if exists
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $this->checkIfValidFormat($header);

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) === 0) continue; // skip empty rows
            $data[] = $row;
        }
        fclose($handle);

        // Determine month/year for notification
        $logDateTimeIndex = array_search('logdatetime', array_map('strtolower', $header));
        $monthYears = [];

        foreach ($data as $row) {
            if (!isset($row[$logDateTimeIndex])) continue;

            $dateStr = trim($row[$logDateTimeIndex]);
            try {
                $carbon = Carbon::parse($dateStr);
                $monthYears[] = ['month' => $carbon->format('M'), 'year' => $carbon->format('Y')];
            } catch (\Exception $e) {
                \Log::warning('Invalid date in CSV', ['value' => $dateStr]);
            }
        }

        $monthYears = array_unique(array_map(fn($item) => $item['month'] . ' ' . $item['year'], $monthYears));
        sort($monthYears);

        $yearsOnly = array_unique(array_map(fn($str) => explode(' ', $str)[1], $monthYears));
        if (count($yearsOnly) === 1) {
            $monthsOnly = array_map(fn($str) => explode(' ', $str)[0], $monthYears);
            $this->monthYear = implode(', ', $monthsOnly) . ' ' . $yearsOnly[0];
        } else {
            $this->monthYear = implode(', ', $monthYears);
        }

        // Save chunks to temp folder
        $chunks = array_chunk($data, 500);
        foreach ($chunks as $index => $chunkData) {
            $chunkFilePath = $tempPath . "/tmp_{$index}.csv";
            $chunkDataWithHeader = array_merge([$header], $chunkData);

            $csvContent = '';
            foreach ($chunkDataWithHeader as $row) {
                $csvContent .= implode(',', array_map(fn($col) => "\"$col\"", $row)) . "\n";
            }

            file_put_contents($chunkFilePath, $csvContent);
        }

        $this->isParsing = false;
        $this->file = null;

    } catch (\Exception $e) {
        $this->addError('file', 'Error processing CSV: ' . $e->getMessage());
        $this->isParsing = false;
        $this->file = null;
    }
}

    public function upload_file()
{
    if (Gate::denies('write timelogs')) {
        return $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Access Denied!', 
            'showAlert' => true,
            'message' => 'You do not have permission to perform this action.',
        ]);
    }

    if (!$this->upload_preview) {
        return $this->dispatch('alert', [
            'status' => 'warning',
            'title' => 'Please be informed',
            'showAlert' => true,
            'message' => 'File logs are required!',
        ]);
    }

    try {
        $files = glob($this->tempPath . '/*.csv');
        $jobs = [];

        foreach ($files as $file) {
            $handle = fopen($file, 'r');
            if (!$handle) continue;

            $header = fgetcsv($handle);
            $data = [];
            while (($row = fgetcsv($handle)) !== false) {
                if (count(array_filter($row)) === 0) continue; // skip empty
                $data[] = array_combine($header, $row);
            }
            fclose($handle);

            if (!empty($data)) {
                // add origin
                $data = array_map(fn($row) => array_merge($row, ['origin' => 'biometrics']), $data);
                $jobs[] = new TimelogUploadProcess($data);
            }

            unlink($file);
        }

        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->withOption('actionBy', ['id' => $this->actionBy->id, 'name' => $this->actionBy->name])
                ->name('Timekeeping Upload For ' . $this->monthYear)
                ->catch(function ($batch, \Throwable $e) {
                    Log::error('Timekeeping batch error: ' . $e->getMessage());
                    $this->actionBy?->notify(new Notifications(
                        'error',
                        'Error during timelogs upload',
                        route('system.jobs', ['id' => $batch->id]),
                        'admin'
                    ));
                })
                ->then(function ($batch) {
                    $this->actionBy?->notify(new Notifications(
                        'success',
                        'Timelogs upload completed successfully',
                        route('system.jobs', ['id' => $batch->id]),
                        'admin'
                    ));
                })
                ->dispatch();
        }

        $this->dispatch('alert', [
            'status' => 'info',
            'title' => 'Please be informed',
            'showAlert' => true,
            'message' => 'Timelogs upload started. You will be notified when done.',
        ]);

    } catch (\Exception $e) {
        $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Oops!',
            'showAlert' => true,
            'message' => 'Error: ' . $e->getMessage(),
        ]);
    } finally {
        $this->isUploading = false;
    }
}


    private function checkIfValidFormat($headers) {

        $requiredHeaders = [
            'bsdno',
            'logdatetime',
            'type',
        ];

        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (!empty($missingHeaders)) {
            throw new \Exception('Invalid csv file for timelogs upload!');
        }
    }

    public function render() {
        return view('livewire.admin.timekeeping.upload');
    }
}
