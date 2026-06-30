<?php

namespace App\Jobs;

use App\Models\EmployeeTimelogs;
use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\DB;
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
    public $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $progressKey)
    {
        $this->data = $data;
        $this->progressKey = $progressKey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    if ($this->batch()?->cancelled()) {
        Log::info('Timelog upload batch cancelled.');
        return;
    }

    $attendanceRecords = [];
    $timelogRecords = [];

   /* $grouped = collect($this->data)
        ->filter(fn ($row) => !empty($row['bsdno']) && !empty($row['logdatetime']))
        ->groupBy(function ($row) {
            return $row['bsdno'] . '|' .
                Carbon::parse($row['logdatetime'])->format('Y-m-d');
        }); */

        $grouped = collect($this->data)

        ->filter(function ($row) {
    
            if (empty($row['bsdno']) || empty($row['logdatetime'])) {
                return false;
            }
    
            // Skip header rows
            if (
                strtolower(trim($row['bsdno'])) === 'bsdno' ||
                strtolower(trim($row['logdatetime'])) === 'logdatetime' ||
                strtolower(trim($row['logdatetime'])) === 'date time'
            ) {
                return false;
            }
    
            try {
    
                Carbon::parse($row['logdatetime']);
    
                return true;
    
            } catch (\Throwable $e) {
    
                Log::warning('Skipping invalid row', [
                    'row' => $row,
                ]);
    
                return false;
    
            }
    
        })
    
        ->groupBy(function ($row) {
    
            return trim($row['bsdno']) . '|' .
                Carbon::parse($row['logdatetime'])->format('Y-m-d');
    
        });    

    foreach ($grouped as $rows) {

        $rows = collect($rows)
            ->sortBy(fn ($row) => Carbon::parse($row['logdatetime']))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Resolve Employee Once
        |--------------------------------------------------------------------------
        */

        $first = $rows->first();

        $employee = EmployeeInformation::where('bsd_no', $first['bsdno'])
            ->orWhere('employee_no', $first['bsdno'])
            ->first();

        if (!$employee) {

            Log::warning('Employee not found.', [
                'uploaded_value' => $first['bsdno'],
            ]);

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Existing Records Once Per Employee Per Day
        |--------------------------------------------------------------------------
        */

        $date = Carbon::parse($first['logdatetime'])->toDateString();

      

        $attendanceExists = false;

        if (!empty($employee->bsd_no)) {

            $attendanceExists = !empty($employee->bsd_no)
            && DB::connection('mysql2')
                ->table('attendances')
                ->where('employee_id', $employee->bsd_no)
                ->whereDate('timestamp', $date)
                ->exists();

        }

        if ($attendanceExists) {

            $deleted = DB::connection('mysql2')
                ->table('attendances')
                ->where('employee_id', $employee->bsd_no)
                ->whereDate('timestamp', $date)
                ->delete();

            Log::info('Deleted biometric attendance before import.', [
                'employee_id' => $employee->bsd_no,
                'date' => $date,
                'deleted' => $deleted,
            ]);

        } else {

            $deleted = DB::connection('mysql')
                ->table('timelogs')
                ->where('employee_id', $employee->employee_no)
                ->whereDate('timestamp', $date)
                ->delete();

            Log::info('Deleted web timelogs before import.', [
                'employee_id' => $employee->employee_no,
                'date' => $date,
                'deleted' => $deleted,
            ]);

        }

        foreach ($rows as $index => $item) {

            try {
                $timestamp = Carbon::parse($item['logdatetime'])
                    ->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {

                Log::warning('Invalid timestamp', [
                    'employee' => $item['bsdno'],
                    'value' => $item['logdatetime'],
                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Determine IN / OUT
            |--------------------------------------------------------------------------
            */

            if (!empty($item['type'])) {

                $status = strtoupper(trim($item['type']));

                $status = match ($status) {
                    'IN'  => 0,
                    'OUT' => 1,
                    default => (int) $status,
                };

            } else {

                $status = $index % 2;

            }

           

            /*
            |--------------------------------------------------------------------------
            | BIOMETRIC (mysql2.attendances)
            |--------------------------------------------------------------------------
            */

            if (!empty($employee->bsd_no)) {

                $attendanceRecords[] = [
                    'sn'          => 'RUU5242500021',
                    'table'       => 'ATTLOG',
                    'stamp'       => '9999',
                    'employee_id' => $employee->bsd_no,
                    'timestamp'   => $timestamp,
                    'status1'     => $status,
                    'isWeb'       => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
        
            } else {
        
                $timelogRecords[] = [
                    'employee_id' => $employee->employee_no,
                    'timestamp'   => $timestamp,
                    'status'      => $status,
                    'isWeb'       => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
        
            }

        }

            /*
            |--------------------------------------------------------------------------
            | WEB TIMELOGS (mysql.timelogs)
            |--------------------------------------------------------------------------
            */

            

        

    }

    if (!empty($attendanceRecords)) {

        DB::connection('mysql2')
            ->table('attendances')
            ->insert($attendanceRecords);

        Log::info('Attendance uploaded.', [
            'count' => count($attendanceRecords),
        ]);

    }

    if (!empty($timelogRecords)) {

        DB::connection('mysql')
            ->table('timelogs')
            ->insert($timelogRecords);

        Log::info('Timelogs uploaded.', [
            'count' => count($timelogRecords),
        ]);

    }

        /*
    |--------------------------------------------------------------------------
    | Update Progress
    |--------------------------------------------------------------------------
    */
    Log::info([
        'progressKey' => $this->progressKey,
        'cache' => cache()->get($this->progressKey),
    ]);

    $progress = cache()->get($this->progressKey);

    if ($progress) {

        $progress['processed'] += count($this->data);

        cache()->put(
            $this->progressKey,
            $progress,
            now()->addHour()
        );

        Log::info('Progress updated', $progress);
    }
}


    public function failed(Throwable $exception)
    {
        Log::error('TimelogUploadProcess failed: ' . $exception->getMessage());
    }
}
