<?php

namespace App\Livewire\Admin\Payroll;

use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\GSISBilling;
use App\Models\Payroll;
use Carbon\Carbon;
use Livewire\Component;

class Process extends Component
{

    public $header;
    public $payroll_id;
    public $records;
    public $isApproved = false;

    protected $listeners = ['save'];


    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        
        $payroll_service = new PayrollService;

        $payroll = Payroll::find($this->payroll_id);

        $this->isApproved = $payroll->status == 'approved' ? true : false;
        
        $payroll = $payroll_service->getData($payroll);

        return $this->records = $payroll;
    }

    public function save(bool $isNotify = true) {

        if($isNotify) {
            
            $title = 'Are you sure to continue?';
            $message = 'Please be informed that once proceed payslip will be released to the employees. This action cannot be reverted';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {
            $payroll = Payroll::find($this->payroll_id);
            $payroll->status = 'approved';
            $payroll->save();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Payroll was approved, Payslip will be visible to employees',
                'redirect' => route('payroll.process', ['payroll_id' => $this->payroll_id])
            ]);
        }

    }

    public function render()
    {
        return view('livewire.admin.payroll.process');
    }
}
