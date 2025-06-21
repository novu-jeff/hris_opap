<?php

namespace App\Livewire\Admin\Hris;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\EmployeeInformation;
use App\Jobs\ChangeEmployeeNoJob;


class ChangeEmployeeNo extends Component
{

    public string $current_employee_no;
    public string $new_employee_no = '';

    protected $listeners = ['setEmployeeNo', 'save'];

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
        ChangeEmployeeNoJob::dispatch($oldEmployeeNo, $newEmployeeNo);

        $this->reset(['current_employee_no', 'new_employee_no']);
        $this->dispatch('loadRecords');
        
        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Queued',
            'showAlert' => true,
            'message' => 'Employee number change has been queued for processing.',
        ]);
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
