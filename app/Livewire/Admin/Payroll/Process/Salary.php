<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\SalaryService;
use App\Models\Payroll;
use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Salary extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;
    public $hdmf = [];
    public $uca = [];
    public $dbp = [];
    public $kawani = [];
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

        $service = app(SalaryService::class);

        $records = $service->getPayroll($this->payroll_id);

        $employmentType = $records['payroll']['employment_type'] ?? null;
        $this->employment_type = strtolower($employmentType['name'] ?? ''); 

        foreach ($records['payroll_items'] as $sectionIndex => $sectionGroup) {
            $employees = $sectionGroup['employees'] ?? [];

            foreach ($employees as $employeeIndex => $record) {
                $this->hdmf[$sectionIndex][$employeeIndex]   = $record['hdmf'] ?? 0;
                $this->uca[$sectionIndex][$employeeIndex]    = $record['uca'] ?? 0;
                $this->dbp[$sectionIndex][$employeeIndex]    = $record['dbp'] ?? 0;
                $this->kawani[$sectionIndex][$employeeIndex] = $record['kawani'] ?? 0;
            }
        }

        $this->originalItems = json_decode(json_encode($records['payroll_items']), true);

        if(is_null($records['payroll']['batch_id'])) {
            return redirect()->route('payroll.index');
        }

        $this->isApproved = $records['payroll']['status'] == 'approved' ? true : false;
        $this->records = $records;
        $this->batchId = $records['batch_id'];

    }

    public function recompute($sectionIndex, $employeeIndex)
    {
        $payroll = &$this->records['payroll'];
        $payroll_item = &$this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

        $payroll_item['hdmf']   = round(floatval($this->hdmf[$sectionIndex][$employeeIndex] ?? 0), 2);
        $payroll_item['uca']    = round(floatval($this->uca[$sectionIndex][$employeeIndex] ?? 0), 2);
        $payroll_item['dbp']    = round(floatval($this->dbp[$sectionIndex][$employeeIndex] ?? 0), 2);
        $payroll_item['kawani'] = round(floatval($this->kawani[$sectionIndex][$employeeIndex] ?? 0), 2);

        $fields = [
            'rlip', 'hdmf', 'philhealth', 'consoloan', 'emergency_loan',
            'plreg', 'mpl', 'cpl', 'mp2', 'mplstlms', 'cir375_cir449',
            'uca', 'dbp', 'kawani', 'w_tax', 'aut'
        ];

        $totalDeduction = round(array_sum(array_map(
            fn($field) => round(floatval($payroll_item[$field] ?? 0), 2),
            $fields
        )), 2);

        $gross = round(floatval($payroll_item['gross_amount_earned'] ?? 0), 2);
        $net = round($gross - $totalDeduction, 2);
        $half = round($net / 2, 2);

        $payroll_item['total_deductions'] = $totalDeduction;
        $payroll_item['net_amount'] = $net;
        $payroll_item['lbp_payroll_account'] = $net;
        $payroll_item['salary'] = $half;

        $original = $this->originalItems[$sectionIndex]['employees'][$employeeIndex] ?? null;
        if ($original) {
            $hasChanged = $this->isChanged($payroll_item, $original);
            $item_id = $payroll_item['id'];

            if ($hasChanged) {
                if (!in_array($item_id, $this->updatedItems)) {
                    $this->updatedItems[] = $item_id;
                }
                $this->hasChanges = true;
            } else {
                $key = array_search($item_id, $this->updatedItems);
                if ($key !== false) {
                    unset($this->updatedItems[$key]);
                    $this->updatedItems = array_values($this->updatedItems); // reindex
                }
                $this->hasChanges = !empty($this->updatedItems);
            }
        }

        $overallNet = 0;
        foreach ($this->records['payroll_items'] as $section) {
            foreach ($section['employees'] as $employee) {
                $overallNet += round(floatval($employee['net_amount'] ?? 0), 2);
            }
        }

        $payroll['overall_net_amount'] = round($overallNet, 2);
        $payroll['overall_salary_amount'] = round($overallNet / 2, 2);


        \Log::debug('Payroll recomputed', [
            'section' => $sectionIndex,
            'employee' => $employeeIndex,
            'net' => $payroll_item['net_amount'],
            'deductions' => $payroll_item['total_deductions'],
            'overall_net' => $payroll['overall_net_amount']
        ]);
    }


    protected function isChanged(array $current, array $original): bool
    {
        foreach ($current as $key => $value) {
            if (array_key_exists($key, $original)) {
                if (number_format((float)$value, 2, '.', '') !== number_format((float)$original[$key], 2, '.', '')) {
                    return true;
                }
            }
        }
        return false;
    }

    public function save(bool $isNotify = true)
    {
        if ($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'We\'ve noticed that there are changes made. Are you sure to save this action first?',
                'action' => 'save'
            ]);

            return;
        }

        DB::beginTransaction();

        try {

            foreach ($this->records['payroll_items'] as $section) {
                foreach ($section['employees'] as $employeeData) {
                    if (empty($employeeData['id'])) {
                        continue;
                    }

                    $payrollItem = SalaryItemsPayroll::find($employeeData['id']);
                    if (!$payrollItem) {
                        continue;
                    }

                    $updateData = [
                        'rlip'                => $employeeData['rlip'] ?? 0,
                        'hdmf'                => $employeeData['hdmf'] ?? 0,
                        'philhealth'          => $employeeData['philhealth'] ?? 0,
                        'consoloan'           => $employeeData['consoloan'] ?? 0,
                        'emergency_loan'      => $employeeData['emergency_loan'] ?? 0,
                        'plreg'               => $employeeData['plreg'] ?? 0,
                        'mpl'                 => $employeeData['mpl'] ?? 0,
                        'cpl'                 => $employeeData['cpl'] ?? 0,
                        'mp2'                 => $employeeData['mp2'] ?? 0,
                        'mplstlms'            => $employeeData['mplstlms'] ?? 0,
                        'cir375_cir449'       => $employeeData['cir375_cir449'] ?? 0,
                        'uca'                 => $employeeData['uca'] ?? 0,
                        'dbp'                 => $employeeData['dbp'] ?? 0,
                        'kawani'              => $employeeData['kawani'] ?? 0,
                        'w_tax'               => $employeeData['w_tax'] ?? 0,
                        'aut'                 => $employeeData['aut'] ?? 0,
                        'total_deductions'    => $employeeData['total_deductions'] ?? 0,
                        'net_amount'          => $employeeData['net_amount'] ?? 0,
                        'lbp_payroll_account' => $employeeData['lbp_payroll_account'] ?? 0,
                        'salary'              => $employeeData['salary'] ?? 0,
                    ];

                    $payrollItem->update($updateData);
                }
            }

            DB::commit();

            $this->hasChanges = false;

            $this->reset('updatedItems');

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Yey!',
                'message' => 'Changes Saved',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Payroll save failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('closeModal', ['modal' => 'loading']);

            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Error occurred: ' . $e->getMessage(),
                'redirect' => '_reload'
            ]);
        }
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

            $payroll = SalaryPayroll::find($this->payroll_id);
            $payroll->status = 'approved';
            $payroll->save();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => 'Payroll was approved, Payslip will be visible to employees',
                'redirect' => route('payroll.process', ['type' => $this->type, 'payroll_id' => $this->payroll_id])
            ]);
        }

    }

    public function render()
    {
        return view('livewire.admin.payroll.process.salary');
    }
}
