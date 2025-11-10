<?php

namespace App\Jobs\Parallel;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class Overtime implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $cut_off_period;

    /**
     * Create a new job instance.
     */
    public function __construct(string $cut_off_period, array $data)
    {
        $this->data = $data;
        $this->cut_off_period = $cut_off_period;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $item) {
            DB::table('parallel_overtime')->insert([
                'employee_no'             => $item[0] ?? '',
                'total_overtime'          => $item[2] ?? '0',
                'total_overtime_hrs'      => $item[3] ?? '0',
                'total_night_diff_amount' => $item[4] ?? '0',
                'total_night_diff_hrs'    => $item[5] ?? '0',
                'cut_off_period'          => $this->cut_off_period,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }
    }
}
