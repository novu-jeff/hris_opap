<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\Services\EmployeeUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $chunk;
    protected $sheetName;
    protected $schedules;

    public function __construct(array $chunk, string $sheetName, array $schedules)
    {
        $this->chunk = $chunk;
        $this->sheetName = $sheetName;
        $this->schedules = $schedules;
    }

   public function handle()
{
    $service = new EmployeeUploadService;

    try {
        // Log basic info about the sheet being processed
        Log::info('Processing sheet', [
            'raw' => $this->sheetName,
            'normalized' => strtolower($this->sheetName),
            'rows' => count($this->chunk),
            'first_employee_no' => $this->chunk[0][0] ?? null
        ]);

        // Match sheet names and call the corresponding service method
        match ($this->sheetName) {
            'Employee Information' => $service->uploadEmployeeInformation($this->chunk, $this->schedules),
            'Family Background' => $service->uploadFamilyBackground($this->chunk),
            'Children' => $service->uploadChildren($this->chunk),
            'Education' => $service->uploadEducation($this->chunk),
            'Employment History' => $service->uploadEmploymentHistory($this->chunk),
            'Civil Service' => $service->uploadCivilService($this->chunk),
            'Trainings' => $service->uploadTrainings($this->chunk),
            'Other Works' => $service->uploadOtherWorks($this->chunk),
            'Skills' => $service->uploadSkills($this->chunk),
            default => Log::warning('No handler matched for sheet', [
                'sheet' => $this->sheetName,
                'rows' => count($this->chunk),
            ])
        };

        Log::info('Sheet processing complete', ['sheet' => $this->sheetName]);

    } catch (Throwable $e) {
        Log::error("Error processing sheet '{$this->sheetName}': " . $e->getMessage(), [
            'sheet' => $this->sheetName,
            'chunk' => $this->chunk,
            'trace' => $e->getTraceAsString()
        ]);
    }
}


}