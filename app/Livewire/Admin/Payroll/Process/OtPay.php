<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\OverTimeService;
use App\Models\OTPayroll;
use App\Models\Payroll;
use App\Models\PayrollItems;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OtPay extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;
    public $amount = [];
    public array $originalItems = [];
    public array $updatedItems = [];
    public bool $isApproved = false;
    public bool $hasChanges = false;
    public $records;

    protected $listeners = ['save', 'approve'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $this->product = config('app.product');

        $service = app(OverTimeService::class);

        $records = $service->getPayroll($this->payroll_id);

        foreach ($records['payroll_items'] as $sectionIndex => $sectionGroup) {
            $employees = $sectionGroup['employees'] ?? [];

            foreach ($employees as $employeeIndex => $record) {
                $this->amount[$sectionIndex][$employeeIndex]   = $record['amount'] ?? 0;
            }
        }

        $this->originalItems = json_decode(json_encode($records['payroll_items']), true);

        if(is_null($records['payroll']['batch_id'])) {
            return redirect()->route('payroll.index');
        }

        $this->records = $records;

        $this->batchId = $records['batch_id'];

    }

    public function approve(bool $isNotify = true) {

        if($isNotify) {
            
            $title = 'Are you sure to continue?';
            $message = 'Please be informed that once proceed payslip will be released to the employees. This action cannot be reverted';
            $action = 'approve';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {
            $payroll = OTPayroll::find($this->payroll_id);
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
        return view('livewire.admin.payroll.process.ot-pay');
    }
}
