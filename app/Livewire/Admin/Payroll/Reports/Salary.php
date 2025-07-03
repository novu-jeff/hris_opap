<?php

namespace App\Livewire\Admin\Payroll\Reports;

use App\Models\EmployementTypes;
use App\Models\SalaryPayroll;
use Illuminate\Support\Facades\Bus;
use Livewire\Component;

class Salary extends Component
{
    public $payroll_id, $status, $entries, $type, $employment_type;
    public $records;
    protected $listeners = ['regeneratePayroll', 'removePayroll', 'cancelPayroll'];

    public function regeneratePayroll($payroll_id) {

        $payroll = SalaryPayroll::findOrFail($payroll_id);

        $this->payroll_id = $payroll_id;

        $this->dispatch('start-job-dispatch', [
            'payroll_id' => $payroll->id,
            'employment_type' => $payroll->employment_type,
            'type' => 'salary'
        ]);

    }

    public function cancelPayroll(bool $isNotify = true)
    {
        
        if($isNotify) {
            $title = 'Are you sure to cancel the current process?';
            $message = 'Please be informed that by proceeding, the entire process finished will be undone and removed';
            $action = 'cancelPayroll';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action,
            ]);
        } else {
            $this->deletePayroll($this->payroll_id);
        }        
    }

    public function removePayroll(bool $isNotify, int $payroll_id = null)
    {
        if($isNotify) {
            $title = 'Are you sure to remove this payroll?';
            $message = 'Please be informed that by proceeding, all data that is connected to this payroll process will be permanently deleted.';
            $action = 'removePayroll';

            $this->payroll_id = $payroll_id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action,
            ]);
        } else {
            $this->deletePayroll($this->payroll_id);
        }
    }

    public function deletePayroll($payroll_id) {
        $payroll = SalaryPayroll::find($payroll_id);

        if ($payroll) {
            $batchId = $payroll->batch_id;

            $payroll->delete();

            if ($batchId) {
                Bus::findBatch($batchId)?->delete();
            }
        }

        return;
    }

    public function render()
    {
    
        $employment_type_id = EmployementTypes::where('name', 'like', '%' . $this->employment_type . '%')->value('id');
        
        $records = SalaryPayroll::where('employment_type', $employment_type_id)
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->paginate($this->entries);

        return view('livewire.admin.payroll.reports.salary', [
            'salary' => $records
        ]);
    }
}
