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
    protected string $oldEmployeeNo;
    protected string $newEmployeeNo;

    public function __construct(string $model, string $oldEmployeeNo, string $newEmployeeNo)
    {

        \Log::info("rey {$model}:  found");
           
        $this->model = $model;
        $this->oldEmployeeNo = $oldEmployeeNo;
        $this->newEmployeeNo = $newEmployeeNo;
    }

    public function handle(): void
    {

        \Log::info("reynaldo here hadle {$this->model}:  found");

        if($this->model == 'App\Models\EmployeeTimelogs'){
            $this->model::where('employee_id', $this->oldEmployeeNo)
            ->update(['employee_id' => $this->newEmployeeNo]);
        }else{
            $this->model::where('employee_no', $this->oldEmployeeNo)
            ->update(['employee_no' => $this->newEmployeeNo]);
        }

        //event(new \App\Events\EmployeeMigrationProgress($this->model));
       // $this->model::where('employee_no', $this->oldEmployeeNo)
      //      ->update(['employee_no' => $this->newEmployeeNo]);
    }
    
}
