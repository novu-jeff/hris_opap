<?php

namespace App\Livewire\Admin\Payroll\Reports;

use App\Models\BonusPayroll;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use Illuminate\Support\Facades\Bus;
use Livewire\Component;

class MidYear extends Component
{
    public $payroll_id, $status, $entries, $type, $employment_type;
    public $records;
    protected $listeners = ['regeneratePayroll', 'removePayroll', 'cancelPayroll'];

    public function regeneratePayroll($payroll_id) {

        $payroll = BonusPayroll::findOrFail($payroll_id);
        $this->payroll_id = $payroll_id;

        $this->dispatch('start-job-dispatch', [
            'payroll_id' => $payroll->id,
            'employment_type' => $payroll->employment_type,
            'type' => $this->type,
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

        $payroll = BonusPayroll::find($payroll_id);

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
        $employment_type_id = EmployementTypes::where(
            'name',
            'like',
            '%' . $this->employment_type . '%'
        )->value('id');

        $records = BonusPayroll::where('bonus_type', $this->type)
            ->where('employment_type', $employment_type_id)
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('payroll_date', 'desc')
            ->paginate($this->entries);

        /*
        |--------------------------------------------------------------------------
        | GROUP ONLY CURRENT PAGE
        |--------------------------------------------------------------------------
        |
        | Same logic as EME accordion UI
        |
        */

        $grouped = $records->getCollection()->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->payroll_date)
                ->format('F Y');
        });

        return view('livewire.admin.payroll.reports.mid-year', [
            'salary' => $records,
            'groupedSalary' => $grouped,
        ]);
    }
}
