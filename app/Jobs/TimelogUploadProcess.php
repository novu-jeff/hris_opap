<?php

namespace App\Jobs;

use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class TimelogUploadProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {    
        foreach ($this->data as $item) {
            EmployeeTimelogs::updateOrCreate(
                ['biometricdtrid' => $item['biometricdtrid']], 
                [
                    'origin' => $item['origin'] ?? null,
                    'bsd_no' => $item['bsdno'] ?? null,
                    'isindtr' => $item['isindtr'] ?? null,
                    'logdatetime' => !empty($item['logdatetime']) 
                        ? Carbon::createFromFormat('d/m/Y H:i:s', $item['logdatetime'])->format('d/m/Y H:i') 
                        : null,
                    'nfcdeviceid' => $item['nfcdeviceid'] ?? null,
                    'type' => $item['type'] ?? null,
                    'ismanual' => $item['ismanual'] ?? null,
                    'captured_image' => $item['captured_image'] ?? null,
                    'captured_location' => $item['captured_location'] ?? null,
                ]
            );
        }
    }

    public function failed(Throwable $exception) {
        // send notification;
    }
}
