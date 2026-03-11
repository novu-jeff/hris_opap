<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;

class ChangeEmployeeNoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected string $model;
    protected string $column;
    protected string $oldEmployeeNo;
    protected string $newEmployeeNo;

    public function __construct(string $model, string $oldEmployeeNo, string $newEmployeeNo, string $column = 'employee_no')
    {
        $this->model = $model;
        $this->column = $column;
        $this->oldEmployeeNo = $oldEmployeeNo;
        $this->newEmployeeNo = $newEmployeeNo;
    }

    public function handle(): void
    {
        // External biometrics attendances uses numeric employee_id (BSD no).
        // Changing employee_no must not rewrite that numeric key with alphanumeric values.
        if ($this->model === 'App\Models\EmployeeTimelogs' && config('app.external_timelogs')) {
            return;
        }

        $this->model::where($this->column, $this->oldEmployeeNo)
            ->update([$this->column => $this->newEmployeeNo]);
    }
    
}
