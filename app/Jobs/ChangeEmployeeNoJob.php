<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ChangeEmployeeNoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected string $model;
    protected string $oldEmployeeNo;
    protected string $newEmployeeNo;
    protected string $column;

    public function __construct(string $model, string $oldEmployeeNo, string $newEmployeeNo, string $column)
    {
        \Log::info("Job created for {$model}, column={$column}, old={$oldEmployeeNo}, new={$newEmployeeNo}");
        $this->model = $model;
        $this->oldEmployeeNo = $oldEmployeeNo;
        $this->newEmployeeNo = $newEmployeeNo;
        $this->column = $column;
    }

   public function handle(): void
    {
        // Instantiate the model
        $modelInstance = new $this->model;
        $table = $modelInstance->getTable();

        // Check if the column exists
        if (!Schema::hasColumn($table, $this->column)) {
            Log::warning("Skipping {$table}: column {$this->column} does not exist");
            return;
        }

        // Update the records
        $modelInstance->newQuery()
            ->where($this->column, $this->oldEmployeeNo)
            ->update([$this->column => $this->newEmployeeNo]);

        Log::info("Updated {$table} ({$this->column}) from {$this->oldEmployeeNo} to {$this->newEmployeeNo}");
    }

}
