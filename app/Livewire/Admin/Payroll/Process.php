<?php

namespace App\Livewire\Admin\Payroll;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\SocialSecurityBilling;
use App\Models\Payroll;
use App\Models\PayrollItems;
use Carbon\Carbon;
use Livewire\Component;


class Process extends Component
{
    public $payroll_id;
    public $type;
    public $records;
    public int $batchProgress = 0;
    public string|null $batchId = null;
    public bool $isBatchProcessing = false;
    public string $batchStatusMessage = 'Please Wait...';

    protected $listeners = ['save', 'approve'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {


        // $payrollService = new PayrollService;
        // $records = $payrollService->getPayroll($this->payroll_id);

        // $this->employment_type = strtolower(EmployementTypes::findOrFail($records['payroll']['employment_type'])->first()->name);
        // $this->type = $records['payroll']['type'];

        // $this->originalItems = json_decode(json_encode($records['payroll_items']), true);

        // if(is_null($records['payroll']['batch_id'])) {
        //     return redirect()->route('payroll.index');
        // }

        // $this->records = $records;

        // $this->batchId = $records['batch_id'];

        // $this->checkBatchStatus();

    }


    public function render()
    {
        return view('livewire.admin.payroll.process');
    }
}
