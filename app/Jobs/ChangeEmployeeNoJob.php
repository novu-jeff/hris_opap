<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\EmployeeInformation;

class ChangeEmployeeNoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $oldEmployeeNo;
    protected string $newEmployeeNo;

    public function __construct(string $oldEmployeeNo, string $newEmployeeNo)
    {
        $this->oldEmployeeNo = $oldEmployeeNo;
        $this->newEmployeeNo = $newEmployeeNo;
    }

    public function handle(): void
    {
        DB::beginTransaction();

        try {
            $employee = EmployeeInformation::where('employee_no', $this->oldEmployeeNo)->firstOrFail();
            $employee->employee_no = $this->newEmployeeNo;
            $employee->save();

            $relations = [
                \App\Models\EmployeeAccount::class,
                \App\Models\EmployeePersonal::class,
                \App\Models\EmployeeEducation::class,
                \App\Models\EmployeeParents::class,
                \App\Models\EmployeeChildren::class,
                \App\Models\EmployeeEmploymentHistory::class,
                \App\Models\EmployeeCivilService::class,
                \App\Models\EmployeeTrainings::class,
                \App\Models\EmployeeOtherWorks::class,
                \App\Models\EmployeeSkillsHobbies::class,
                \App\Models\LeaveCredits::class,
                \App\Models\EmployeeLeave::class,
                \App\Models\EmployeeBusinessSlip::class,
                \App\Models\EmployeeAtro::class,
                \App\Models\EmployeeAtroRelative::class,
                \App\Models\EmployeeClockInOut::class,
                \App\Models\EmployeeDeductions::class,
                \App\Models\EmployeeEarnings::class,
                \App\Models\EmployeeLeaveCard::class,
                \App\Models\EmployeeRequestLog::class,
                \App\Models\EmployeeUpdateChildren::class,
                \App\Models\EmployeeUpdateCivilService::class,
                \App\Models\EmployeeUpdateEducation::class,
                \App\Models\EmployeeUpdateEmploymentHistory::class,
                \App\Models\EmployeeUpdateOtherWorks::class,
                \App\Models\EmployeeUpdateParents::class,
                \App\Models\EmployeeUpdatePersonal::class,
                \App\Models\EmployeeUpdateSkillsHobbies::class,
                \App\Models\EmployeeUpdateTrainings::class,
            ];

            foreach ($relations as $model) {
                $model::where('employee_no', $this->oldEmployeeNo)
                      ->update(['employee_no' => $this->newEmployeeNo]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e); 
        }
    }
}
