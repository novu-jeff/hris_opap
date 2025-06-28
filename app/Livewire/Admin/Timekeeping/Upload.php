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
use Throwable;
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
        if ($this->file) {
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile && $file->getClientOriginalExtension() === 'csv') {
                try {
                    Storage::delete(Storage::allFiles('public/temp/files'));

                    $fileName = uniqid() . '.csv';
                    $filePath = $file->storeAs('public/temp/files', $fileName);
                    $this->upload_preview = asset('storage/temp/files/' . $fileName);

                    $data = array_map('str_getcsv', explode("\n", file_get_contents(storage_path("app/$filePath"))));
                    $header = $data[0];
                    $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);

                    $this->checkIfValidFormat($header);

                    $chunks = array_chunk($data, 1000);

                    $tempPath = resource_path('temp/' . time());
                    if (!file_exists($tempPath)) {
                        mkdir($tempPath, 0777, true);
                    }

                    $this->tempPath = $tempPath;

                    $logDateTimeIndex = array_search('logdatetime', array_map('strtolower', $header));
                    $monthYears = [];

                    foreach ($data as $rowIndex => $row) {
                        if ($rowIndex === 0) continue;

                        if (isset($row[$logDateTimeIndex])) {
                            $dateStr = trim($row[$logDateTimeIndex]);

                            try {
                                $carbon = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $dateStr);
                                $month = $carbon->format('M');
                                $year = $carbon->format('Y');
                                $monthYears[] = ['month' => $month, 'year' => $year];
                            } catch (\Exception $e) {
                                \Log::info('error: ' . $e->getMessage());
                            }
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

                    foreach ($chunks as $index => $chunkData) {
                        $chunkFileName = "/tmp_{$index}.csv";
                        $chunkFilePath = $tempPath . $chunkFileName;
                        $chunkDataWithHeader = array_merge([$header], $chunkData);
                        $csvContent = implode("\n", array_map(fn($row) => implode(',', $row), $chunkDataWithHeader));

                        file_put_contents($chunkFilePath, $csvContent);
                    }

                    $this->isParsing = false;
                } catch (\Exception $e) {
                    $this->addError('file', 'There was an error saving the file to temporary storage: ' . $e->getMessage());
                    $this->isParsing = false;
                }
            } else {
                $this->addError('file', 'The file must be in CSV format.');
            }
            $this->file = null;
        } else {
            $this->isParsing = true;
        }
    }

    public function upload_file() {

        if (Gate::denies('write timelogs')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if (!$this->upload_preview) {
            return $this->dispatch('alert', [
                'status' => 'warning',
                'title' => 'Please be informed',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'File logs are required!',
            ]);
        }

        try {

            $path = $this->tempPath;
            $files = glob($path . '/*.csv');

            $jobs = []; 

            foreach ($files as $key => $file) {
                $data = array_map('str_getcsv', file($file));

                $header = $data[0]; 
                array_shift($data); 

                if (!empty($data)) {
                    array_shift($data); 
                }

                $formattedData = [];

                foreach ($data as $row) {
                    $formattedRow = array_combine($header, $row);
                    $formattedRow['origin'] = 'biometrics';
                    $formattedData[] = $formattedRow;
                }

                $jobs[] = new TimelogUploadProcess($formattedData); 

                unlink($file);
            }

            if (!empty($jobs)) {

                $batch = Bus::batch($jobs)
                    ->withOption('actionBy', [
                        'id' => $this->actionBy->id,
                        'name' => $this->actionBy->name
                    ])
                    ->name('Timekeeping Upload For ' . $this->monthYear)
                    ->catch(function (Batch $batch, Throwable $e) {
                        $this->actionBy?->notify(new Notifications(
                            'error',
                            'An error occurred during uploading timelogs.',
                            route('system.jobs', ['id' => $batch->id]),
                            'admin'
                        ));
                    })
                    ->then(function (Batch $batch) { 
                        $this->actionBy?->notify(new Notifications(
                            'success',
                            'The uploading of timelogs has been finished.',
                                route('system.jobs', ['id' => $batch->id]),
                            'admin'
                        ));
                    })
                    ->finally(function () {
                        Artisan::call('compute-aut'); 
                    })
                    ->dispatch();
            }

            $this->dispatch('alert', [
                'status' => 'info',
                'title' => 'Please be informed',
                'showAlert' => true,
                'message' => 'The uploading of timelogs has been started. We are currently processing the data. You will receive another notification once the upload is complete. Thank you for your patience.',
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
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
