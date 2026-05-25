<?php

namespace App\Livewire\Admin\Payroll\Reports;

use App\Models\PayrollGratuity;
use App\Models\EmployementTypes;
use Illuminate\Support\Facades\Bus;
use Livewire\Component;
use Livewire\WithPagination;

class Gratuity extends Component
{
    use WithPagination;

    public $payroll_id;
    public $status;
    public $entries = 10;
    public $type = 'gratuity';
    public $employment_type;

    protected $listeners = [
        'regeneratePayroll',
        'removePayroll',
        'cancelPayroll'
    ];

    public function regeneratePayroll($payroll_id)
    {
        $payroll = PayrollGratuity::findOrFail($payroll_id);

        $this->payroll_id = $payroll_id;

        $this->dispatch('start-job-dispatch', [

            'payroll_id' =>
                $payroll->id,

            'employment_type' =>
                $payroll->employment_type,

            'type' => 'gratuity',

        ]);
    }

    public function cancelPayroll(bool $isNotify = true)
    {
        if ($isNotify) {

            $this->dispatch('showConfirmation', [

                'title' =>
                    'Are you sure to cancel the current process?',

                'message' =>
                    'Please be informed that by proceeding, the entire payroll process will be removed.',

                'action' =>
                    'cancelPayroll',

            ]);

        } else {

            $this->deletePayroll($this->payroll_id);

        }
    }

    public function removePayroll(bool $isNotify, int $payroll_id = null)
    {
        if ($isNotify) {

            $this->payroll_id = $payroll_id;

            $this->dispatch('showConfirmation', [

                'title' =>
                    'Are you sure to remove this payroll?',

                'message' =>
                    'All gratuity payroll records connected to this process will be permanently deleted.',

                'action' =>
                    'removePayroll',

            ]);

        } else {

            $this->deletePayroll($this->payroll_id);

        }
    }

    public function deletePayroll($payroll_id)
    {
        $payroll = PayrollGratuity::find($payroll_id);

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

        $records = PayrollGratuity::query()

            ->when(
                $employment_type_id,
                fn($q) =>
                    $q->where(
                        'employment_type',
                        $employment_type_id
                    )
            )

            ->when(
                $this->status,
                fn($q) =>
                    $q->where(
                        'status',
                        $this->status
                    )
            )

            ->orderBy('payroll_date', 'desc')

            ->paginate($this->entries);

        /*
        |--------------------------------------------------------------------------
        | GROUP BY MONTH
        |--------------------------------------------------------------------------
        */

        $grouped = $records->getCollection()

            ->groupBy(function ($item) {

                return \Carbon\Carbon::parse(
                    $item->payroll_date
                )->format('F Y');

            });

        return view(
            'livewire.admin.payroll.reports.gratuity',
            [

                'salary' =>
                    $records,

                'groupedSalary' =>
                    $grouped,

            ]
        );
    }
}