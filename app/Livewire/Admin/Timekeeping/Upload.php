<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Jobs\TimelogUploadProcess;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{

    use WithFileUploads;

    public $file;
    public $upload_preview;
    public object $records;
    public $tempPath;
    public $batchInfo;
    public bool $isParsing, $isUploading = false;
    public $isLoading = false;
    public $batch_id;

    protected $listeners = ['cancelUpload'];

    public function mount() {
        $this->loadingUpload();
    }

    public function updatedFile() {

        if ($this->file) {
            $this->upload_preview;
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile && $file->getClientOriginalExtension() === 'csv') {
                
                try {
                    // Delete existing files in the temp directory
                    Storage::delete(Storage::allFiles('public/temp/files'));
                
                    // Generate unique file name
                    $fileName = uniqid() . '.csv';
                
                    // Store file
                    $filePath = $file->storeAs('public/temp/files', $fileName);
                
                    // Generate preview URL
                    $this->upload_preview = asset('storage/temp/files/' . $fileName);
                
                    // Read and parse CSV data
                    $data = array_map('str_getcsv', explode("\n", file_get_contents(storage_path("app/$filePath"))));
                
                    $header = $data[0];
                    $header = preg_replace('/^\xEF\xBB\xBF/', '', $header); // Remove BOM from the first column

                    $this->checkIfValidFormat($header);

                    // Chunk data into 1000 rows per file
                    $chunks = array_chunk($data, 1000);
                
                    // Create temp directory if it doesn't exist
                    $tempPath = resource_path('temp/' . time());
                    if (!file_exists($tempPath)) {
                        mkdir($tempPath, 0777, true);
                    }

                    $this->tempPath = $tempPath;
                
                    // Store each chunk as a separate file
                    foreach ($chunks as $index => $chunkData) {
                        $chunkFileName = "/tmp_{$index}.csv";
                        $chunkFilePath = $tempPath . $chunkFileName;
                        $chunkDataWithHeader = array_merge([$header], $chunkData);
                        $csvContent = implode("\n", array_map(fn($row) => implode(',', $row), $chunkDataWithHeader));

                        file_put_contents($chunkFilePath, $csvContent);
                    }
                
                    $this->isParsing = false;
                
                } catch (\Exception $e) {

                    $this->addError('file', 'There was an error saving the file to temporary storage.');
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

            // CODE HERE
            $path = $this->tempPath;
            $files = glob($path . '/*.csv');
            
            $batch = Bus::batch([])->dispatch(); 

            foreach ($files as $key => $file) {
    

                $data = array_map('str_getcsv', file($file));

                $header = $data[0]; 

                array_shift($data); 

                $formattedData = [];

                foreach ($data as $row) {
                    $formattedRow = array_combine($header, $row);
                    $formattedRow['origin'] = 'biometrics'; 
                    $formattedData[] = $formattedRow; 
                }
            
                $batch->add(new TimelogUploadProcess($formattedData));
                                
                unlink($file);
            }

            session(['batchInfo' => $batch->id]);

            $this->loadingUpload();
            
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
            'biometricdtrid',
            'bsdno',
            'isindtr',
            'logdatetime',
            'nfcdeviceid',
            'type',
            'ismanual',
        ];

        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (!empty($missingHeaders)) {
            throw new \Exception('Invalid csv file for timelogs upload!');
        }
    }

    public function loadingUpload() {

        $batch_id = session('batchInfo');

        if($batch_id) {
            $this->dispatch('isLoading', $batch_id);
        }

    }

    public function cancelUpload($batchId) {

        $batch = Bus::findBatch($batchId);
    
        if ($batch) {

            $batch->cancel(); 
    
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Yey!', 
                'message' => 'Uploading Cancelled',
                'redirect' => route('timekeeping.upload')
            ]);
        } 
    
        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Oops!', 
            'message' => 'Unable to cancel upload',
            'redirect' => route('timekeeping.upload')
        ]);
    }
    

    public function render() {
        return view('livewire.admin.timekeeping.upload');
    }
}
