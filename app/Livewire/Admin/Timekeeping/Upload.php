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
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
        return;
    }

    $this->resetErrorBag();

    $file = $this->file;

    if (!($file instanceof \Illuminate\Http\UploadedFile)) {
        return;
    }

    $extension = strtolower($file->getClientOriginalExtension());

    $allowed = ['csv', 'xlsx', 'xls', 'dat'];

    if (!in_array($extension, $allowed)) {

        $this->addError(
            'file',
            'Only CSV, Excel (.xlsx/.xls), and DAT files are supported.'
        );

        $this->file = null;
        return;
    }

    $this->isParsing = true;

    try {

        Storage::delete(Storage::allFiles('public/temp/files'));

        $fileName = uniqid() . '.' . $extension;

        $filePath = $file->storeAs(
            'public/temp/files',
            $fileName
        );

        $this->upload_preview = asset(
            'storage/temp/files/' . $fileName
        );

        $this->tempPath = resource_path('temp/' . time());

        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0777, true);
        }

        /*
        |--------------------------------------------------------------------------
        | Read File
        |--------------------------------------------------------------------------
        */

        switch ($extension) {

            case 'csv':
                $rows = $this->readCsv(storage_path("app/$filePath"));
                break;

            case 'xlsx':
            case 'xls':
                $rows = $this->readExcel(storage_path("app/$filePath"));
                break;

            case 'dat':
                $rows = $this->readDat(storage_path("app/$filePath"));
                break;

            default:
                throw new \Exception('Unsupported file.');
        }

        if (empty($rows)) {
            throw new \Exception('No records found.');
        }

        $this->detectMonthYear($rows);
        $this->createChunks($rows);

        $this->file = null;
        $this->isParsing = false;

    } catch (\Throwable $e) {

        $this->isParsing = false;

        $this->file = null;

        $this->addError(
            'file',
            $e->getMessage()
        );
    }
}

private function readCsv(string $path): array
{
    $handle = fopen($path, 'r');

    if (!$handle) {
        throw new \Exception('Unable to open CSV file.');
    }

    $header = fgetcsv($handle);

    if (!$header) {
        fclose($handle);
        throw new \Exception('CSV file is empty.');
    }

    // Remove UTF-8 BOM
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

    // Normalize headers
    $header = array_map(function ($value) {
        return strtolower(trim($value));
    }, $header);

    $this->checkIfValidFormat($header);

    $rows = [];

    while (($row = fgetcsv($handle)) !== false) {

        // Skip blank rows
        if (!array_filter($row, fn($value) => trim((string)$value) !== '')) {
            continue;
        }

        // Fill missing columns
        if (count($row) < count($header)) {
            $row = array_pad($row, count($header), null);
        }

        $record = array_combine($header, $row);

        $rows[] = [
            'bsdno'       => trim($record['bsdno'] ?? ''),
            'logdatetime' => trim($record['logdatetime'] ?? ''),
            'type'        => isset($record['type'])
                                ? trim($record['type'])
                                : null,
        ];
    }

    fclose($handle);

    return $rows;
}

private function readExcel(string $path): array
{
    $spreadsheet = IOFactory::load($path);

    $worksheet = $spreadsheet->getActiveSheet();

    $rows = $worksheet->toArray(null, true, true, false);

    if (empty($rows)) {
        throw new \Exception('Excel file is empty.');
    }

    // First row = headers
    $header = array_shift($rows);

    $header = array_map(function ($value) {
        return strtolower(trim((string) $value));
    }, $header);

    $this->checkIfValidFormat($header);

    $result = [];

    foreach ($rows as $row) {

        // Skip empty rows
        if (!array_filter($row, fn($value) => trim((string)$value) !== '')) {
            continue;
        }

        // Fill missing columns
        if (count($row) < count($header)) {
            $row = array_pad($row, count($header), null);
        }

        $record = array_combine($header, $row);

        /*
        |--------------------------------------------------------------------------
        | Handle Excel Date Cells
        |--------------------------------------------------------------------------
        */

        $logdatetime = $record['logdatetime'] ?? null;

        if (is_numeric($logdatetime)) {
            try {
                $logdatetime = Date::excelToDateTimeObject($logdatetime)
                    ->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $logdatetime = null;
            }
        }

        $result[] = [
            'bsdno' => trim($record['bsdno'] ?? ''),
            'logdatetime' => trim((string) $logdatetime),
            'type' => isset($record['type'])
                ? trim((string) $record['type'])
                : null,
        ];
    }

    return $result;
}

private function readDat(string $path): array
{
    if (!file_exists($path)) {
        throw new \Exception('DAT file not found.');
    }

    $rows = [];

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line);

        if ($line === '') {
            continue;
        }

        // Ignore comments
        if (str_starts_with($line, '#')) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Comma or Semicolon separated
        |--------------------------------------------------------------------------
        |
        | EMP001,2025-11-20 08:00:00
        | EMP001,2025-11-20 08:00:00,IN
        |
        */

        if (str_contains($line, ',') || str_contains($line, ';')) {

            $delimiter = str_contains($line, ';') ? ';' : ',';

            $parts = array_map('trim', explode($delimiter, $line));

        } else {

            /*
            |--------------------------------------------------------------------------
            | Space / Tab separated
            |--------------------------------------------------------------------------
            |
            | EMP001 2025-11-20 08:00:00
            | EMP001    2025-11-20    08:00:00
            |
            */

            $parts = preg_split('/\s+/', $line);
        }

        if (count($parts) < 3) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Build datetime
        |--------------------------------------------------------------------------
        */

        $employee = trim($parts[0]);

        $datetime = trim($parts[1] . ' ' . $parts[2]);

        $type = null;

        if (isset($parts[3])) {
            $type = trim($parts[3]);
        }

        $rows[] = [
            'bsdno'       => $employee,
            'logdatetime' => $datetime,
            'type'        => $type,
        ];
    }

    if (empty($rows)) {
        throw new \Exception('No valid records found in DAT file.');
    }

    return $rows;
}

private function createChunks(array $rows): void
{
    if (empty($rows)) {
        throw new \Exception('No records found to process.');
    }

    if (!file_exists($this->tempPath)) {
        mkdir($this->tempPath, 0777, true);
    }

    $chunks = array_chunk($rows, 500);

    foreach ($chunks as $index => $chunk) {

        $path = $this->tempPath . DIRECTORY_SEPARATOR . "tmp_{$index}.csv";

        $fp = fopen($path, 'w');

        if (!$fp) {
            throw new \Exception("Unable to create temporary file: {$path}");
        }

        // Use the first record as the CSV header
        fputcsv($fp, array_keys($chunk[0]));

        foreach ($chunk as $row) {

            // Ensure all expected columns exist
            $record = [
                'bsdno'       => $row['bsdno'] ?? '',
                'logdatetime' => $row['logdatetime'] ?? '',
                'type'        => $row['type'] ?? '',
            ];

            fputcsv($fp, $record);
        }

        fclose($fp);
    }
}

private function detectMonthYear(array $rows): void
{
    $monthYears = [];

    foreach ($rows as $row) {

        if (empty($row['logdatetime'])) {
            continue;
        }

        try {

            $date = Carbon::parse($row['logdatetime']);

            $monthYears[] = $date->format('M Y');

        } catch (\Throwable $e) {

            \Log::warning('Invalid logdatetime while detecting month/year', [
                'value' => $row['logdatetime'],
            ]);

            continue;
        }
    }

    if (empty($monthYears)) {
        $this->monthYear = now()->format('M Y');
        return;
    }

    $monthYears = array_unique($monthYears);
    sort($monthYears);

    $years = array_unique(array_map(function ($item) {
        return explode(' ', $item)[1];
    }, $monthYears));

    if (count($years) === 1) {

        $months = array_map(function ($item) {
            return explode(' ', $item)[0];
        }, $monthYears);

        $this->monthYear = implode(', ', $months) . ' ' . reset($years);

    } else {

        $this->monthYear = implode(', ', $monthYears);

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

    if (!$this->upload_preview || !is_dir($this->tempPath)) {
        return $this->dispatch('alert', [
            'status' => 'warning',
            'title' => 'Please be informed',
            'showAlert' => true,
            'message' => 'Please upload a file first.',
        ]);
    }

    $this->isUploading = true;

    try {

        $files = glob($this->tempPath . DIRECTORY_SEPARATOR . '*.csv');

        if (empty($files)) {
            throw new \Exception('No temporary files found.');
        }

        $jobs = [];

        foreach ($files as $file) {

            $handle = fopen($file, 'r');

            if (!$handle) {
                continue;
            }

            $header = fgetcsv($handle);

            if (!$header) {
                fclose($handle);
                continue;
            }

            $data = [];

            while (($row = fgetcsv($handle)) !== false) {

                if (!array_filter($row)) {
                    continue;
                }

                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), null);
                }

                $data[] = array_combine($header, $row);
            }

            fclose($handle);

            if (!empty($data)) {

                $jobs[] = new TimelogUploadProcess($data);

            }

            @unlink($file);
        }

        if (empty($jobs)) {
            throw new \Exception('No valid timelog records found.');
        }

        Bus::batch($jobs)

            ->name('Timekeeping Upload - ' . $this->monthYear)

            ->withOption('actionBy', [
                'id' => $this->actionBy->id,
                'name' => $this->actionBy->name,
            ])

            ->catch(function ($batch, \Throwable $e) {

                \Log::error('Timekeeping Upload Failed', [
                    'batch_id' => $batch->id,
                    'message' => $e->getMessage(),
                ]);

                $this->actionBy?->notify(new Notifications(
                    'error',
                    'Timelog upload failed.',
                    route('system.jobs', [
                        'id' => $batch->id
                    ]),
                    'admin'
                ));

            })

            ->then(function ($batch) {

                $this->actionBy?->notify(new Notifications(
                    'success',
                    'Timelog upload completed successfully.',
                    route('system.jobs', [
                        'id' => $batch->id
                    ]),
                    'admin'
                ));

            })

            ->dispatch();

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Upload Started',
            'showAlert' => true,
            'message' => 'Timelog import has started. You will receive a notification when it finishes.',
        ]);

    } catch (\Throwable $e) {

        \Log::error('Timelog Upload Error', [
            'message' => $e->getMessage(),
        ]);

        $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Upload Failed',
            'showAlert' => true,
            'message' => $e->getMessage(),
        ]);

    } finally {

        $this->isUploading = false;

    }
}


   

    private function checkIfValidFormat(array &$headers): void
{
    // Normalize headers
    $headers = array_map(function ($header) {
        return strtolower(trim((string) $header));
    }, $headers);

    /*
    |--------------------------------------------------------------------------
    | Map common biometric headers
    |--------------------------------------------------------------------------
    */

    $aliases = [

        // Employee Number
        'employee no'      => 'bsdno',
        'employee_no'      => 'bsdno',
        'employee number'  => 'bsdno',
        'employee id'      => 'bsdno',
        'employee_id'      => 'bsdno',
        'user id'          => 'bsdno',
        'userid'           => 'bsdno',
        'user_id'          => 'bsdno',
        'enrollnumber'     => 'bsdno',
        'enroll number'    => 'bsdno',
        'enrollno'         => 'bsdno',
        'pin'              => 'bsdno',
        'id'               => 'bsdno',

        // Timestamp
        'timestamp'        => 'logdatetime',
        'date time'        => 'logdatetime',
        'datetime'         => 'logdatetime',
        'punch time'       => 'logdatetime',
        'transaction time' => 'logdatetime',
        'transactiontime'  => 'logdatetime',
        'log time'         => 'logdatetime',
        'logtime'          => 'logdatetime',
        'time'             => 'logdatetime',

        // Status
        'status'           => 'type',
        'status1'          => 'type',
        'state'            => 'type',
        'in/out'           => 'type',
        'inout'            => 'type',
    ];

    foreach ($headers as &$header) {

        if (isset($aliases[$header])) {
            $header = $aliases[$header];
        }

    }

    unset($header);

    /*
    |--------------------------------------------------------------------------
    | Required columns
    |--------------------------------------------------------------------------
    */

    $required = [
        'bsdno',
        'logdatetime',
    ];

    $missing = array_diff($required, $headers);

    if (!empty($missing)) {

        throw new \Exception(
            'Invalid file format. Required columns: bsdno and logdatetime.'
        );

    }
}

    public function render()
    {
        return view('livewire.admin.timekeeping.upload', [
            'supportedFormats' => [
                'CSV (.csv)',
                'Excel (.xlsx)',
                'Excel (.xls)',
                'Biometric DAT (.dat)',
            ],
        ]);
    }
}
