<?php

namespace App\Livewire\Admin\Hris;

use App\Notifications\Notifications;
use DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\EmployeeInformation;
use App\Jobs\ChangeEmployeeNoJob;


class ChangeEmployeeNo extends Component
{


    public $actionBy;
    public string $current_employee_no = '';
    public string $new_employee_no = '';

    protected $listeners = ['setEmployeeNo', 'save'];

    public function mount() {
        $this->actionBy = Auth::user();
    }

    public function setEmployeeNo($employee_no)
    {
        $this->current_employee_no = $employee_no;
    }

    public function save(bool $isNotify = true) {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that there are changes made to the employee number. 
                        This will affect the employee\'s records and access.';
            
            $action = 'save';
            
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $validator = \Validator::make([
                'current_employee_no' => $this->current_employee_no,
                'new_employee_no' => $this->new_employee_no,
            ], [
                'current_employee_no' => 'required|exists:employee_information,employee_no',
                'new_employee_no' => 'required|unique:employee_information,employee_no',
            ]);
            
            if($validator->fails()) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => $validator->errors()->first(),
                ]);
                return;
            }

            $this->changeEmployeeNo($this->current_employee_no, $this->new_employee_no);

        }
    }

    public function changeEmployeeNo(string $oldEmployeeNo, string $newEmployeeNo)
    {
        
        try {

            $newEmployeeNo = strtoupper($newEmployeeNo);

            $employeeModel = EmployeeInformation::where('employee_no', $oldEmployeeNo)->firstOrFail();
            $employeeModel->employee_no = strtoupper($newEmployeeNo);
            $employeeModel->isTransferingEmp = true; 
            $employeeModel->save();

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
                \App\Models\EmployeeLeaveDates::class,
                \App\Models\EmployeeBusinessSlip::class,
                \App\Models\EmployeeAtro::class,
                \App\Models\EmployeeAtroRelative::class,
                \App\Models\EmployeeClockInOut::class,
                \App\Models\EmployeeDeductions::class,
                \App\Models\EmployeeEarnings::class,
                \App\Models\EmployeeLeaveCard::class,
                \App\Models\EmployeeTimeAdjustments::class,
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

            $jobs = [];
            
            foreach ($relations as $model) {
                $jobs[] = new ChangeEmployeeNoJob($model, $oldEmployeeNo, $newEmployeeNo);
            }

            if (!empty($jobs)) {
                $batch = Bus::batch($jobs)
                    ->withOption('actionBy', [
                        'id' => $this->actionBy->id,
                        'name' => $this->actionBy->name
                    ])
                    ->name('Migration: ' . $oldEmployeeNo . ' to ' . $newEmployeeNo)
                    ->catch(function (Batch $batch, \Throwable $e) {
                        $this->actionBy?->notify(new Notifications(
                            'error',
                            'An error occurred during migration of employee.',
                            route('system.jobs', ['id' => $batch->id]),
                            'admin'
                        ));
                    })
                    ->then(function (Batch $batch) { 
                        $this->actionBy?->notify(new Notifications(
                            'success',
                            'Migration of employee has been finished',
                                route('system.jobs', ['id' => $batch->id]),
                            'admin'
                        ));
                    })
                    ->finally(function() use ($employeeModel) {
                        $employeeModel->isTransferingEmp = false;
                        $employeeModel->save();
                    })
                    ->dispatch();
            }

            $this->reset(['new_employee_no']);
            $this->dispatch('loadRecords');
            $this->dispatch('hideModal', [
                'modal' => 'change_employee_no'
            ]);

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'info',
                'showAlert' => true,
                'message' => 'Migration of employee ' . $oldEmployeeNo . ' to ' . $newEmployeeNo . ' has been started. We\'ll notify you once it\'s done. Thank you!',
            ]);

            return;

        } catch (\Exception $e) {

            $this->reset(['new_employee_no']);
            $this->dispatch('hideModal', [
                'modal' => 'change_employee_no'
            ]);

            report($e);

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops',
                'showAlert' => true,
                'message' => 'An error occured: ' . $e->getMessage(),
            ]);

            return;
        }
    }

    
    public function closeModal() 
    {
        $this->reset(['current_employee_no', 'new_employee_no']);
        $this->dispatch('hideModal', [
            'modal' => 'change_employee_no'
        ]);    
    }

    public function render()
    {
        return view('livewire.admin.hris.change-employee-no');
    }
}
