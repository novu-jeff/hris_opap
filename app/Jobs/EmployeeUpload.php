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
            match ($this->sheetName) {
                'EMPLOYEE INFORMATION' => $service->uploadEmployeeInformation($this->chunk, $this->schedules),
                'Family Background' => $service->uploadFamilyBackground($this->chunk),
                'Children' => $service->uploadChildren($this->chunk),
                'Education' => $service->uploadEducation($this->chunk),
                'Employment History' => $service->uploadEmploymentHistory($this->chunk),
                'Civil Service' => $service->uploadCivilService($this->chunk),
                'Trainings' => $service->uploadTrainings($this->chunk),
                'Other Works' => $service->uploadOtherWorks($this->chunk),
                'Skills' => $service->uploadSkills($this->chunk),
                default => null
            };
        } catch (Throwable $e) {
            Log::error("Error processing sheet '{$this->sheetName}': " . $e->getMessage(), [
                'sheet' => $this->sheetName,
                'chunk' => $this->chunk,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}