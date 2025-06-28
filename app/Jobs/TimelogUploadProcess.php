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
        if ($this->batch()->cancelled()) {
            Log::info('Job batch was cancelled.');
            return;
        }

        $records = [];

        foreach ($this->data as $item) {
            $timestamp = null;

            if (!empty($item['logdatetime'])) {
                $formats = ['d/m/Y H:i:s', 'd/m/Y H:i'];

                foreach ($formats as $format) {
                    try {
                        $timestamp = Carbon::createFromFormat($format, $item['logdatetime'])->format('Y-m-d H:i:s');
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (!$timestamp) {
                    continue;
                }
            }

            $records[] = [
                'sn' => 'RUU5242500021',
                'table' => 'ATTLOG',
                'stamp' => '9999',
                'employee_id' => $item['bsdno'] ?? null,
                'timestamp' => $timestamp,
                'status1' => $item['type'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

        }

        if (!empty($records)) {
            EmployeeTimelogs::insert($records);
        }
    }


    public function failed(Throwable $exception)
    {
        Log::error('TimelogUploadProcess failed: ' . $exception->getMessage());
    }
}
