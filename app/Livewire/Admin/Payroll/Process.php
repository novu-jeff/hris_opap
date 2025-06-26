<?php

namespace App\Livewire\Admin\Payroll;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use App\Models\GSISBilling;
use App\Models\Payroll;
use App\Models\PayrollItems;
use Carbon\Carbon;
use Livewire\Component;


class Process extends Component
{

    public $header;
    public $payroll_id;
    public $records;
    public $page;
    public bool $isApproved = false;
    public bool $hasChanges = false;
    public $error;

    public int $batchProgress = 0;
    public string|null $batchId = null;
    public bool $isBatchProcessing = false;
    public string $batchStatusMessage = 'Please Wait...';

    public array $originalItems = [];
    public array $updatedItems = [];

    public $hdmf = [];
    public $uca = [];
    public $dbp = [];
    public $kawani = [];

    protected $listeners = ['save', 'approve'];


    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $payrollService = new PayrollService;
        $records = $payrollService->getPayroll($this->payroll_id);

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

        $this->records = $records;

        $this->batchId = $records['batch_id'];

        $this->checkBatchStatus();

    }

    public function checkBatchStatus()
    {
        if (!$this->batchId) return;

        $batch = Bus::findBatch($this->batchId);

        if ($batch && !$batch->finished()) {

            $this->isBatchProcessing = true;
            $this->batchProgress = $batch->progress();

            $this->batchStatusMessage = match (true) {
                $this->batchProgress < 10   => 'Retrieving employee records...',
                $this->batchProgress < 60   => 'Calculating salaries, deductions, and benefits...',
                $this->batchProgress < 80   => 'Generating payroll items and inserting records...',
                $this->batchProgress < 95   => 'Finalizing reports...',
                $this->batchProgress <= 100 => 'Redirecting...',
            };
        } else {
            $this->isBatchProcessing = false;
        }
    }

    public function recompute($sectionIndex, $employeeIndex)
    {
        $payroll = &$this->records['payroll'];
        $payroll_item = &$this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

        // Update selected employee deductions
        $payroll_item['hdmf']   = floatval($this->hdmf[$sectionIndex][$employeeIndex] ?? 0);
        $payroll_item['uca']    = floatval($this->uca[$sectionIndex][$employeeIndex] ?? 0);
        $payroll_item['dbp']    = floatval($this->dbp[$sectionIndex][$employeeIndex] ?? 0);
        $payroll_item['kawani'] = floatval($this->kawani[$sectionIndex][$employeeIndex] ?? 0);

        $fields = [
            'rlip', 'hdmf', 'philhealth', 'consoloan', 'emergency_loan',
            'plreg', 'mpl', 'cpl', 'mp2', 'mplstlms', 'cir375_cir449',
            'uca', 'dbp', 'kawani', 'w_tax', 'aut'
        ];

        // Recompute current employee totals
        $totalDeduction = array_sum(array_map(
            fn($field) => floatval($payroll_item[$field] ?? 0),
            $fields
        ));

        $gross = floatval($payroll_item['gross_amount_earned'] ?? 0);
        $net = $gross - $totalDeduction;

        $payroll_item['total_deductions'] = number_format($totalDeduction, 2, '.', '');
        $payroll_item['net_amount'] = number_format($net, 2, '.', '');
        $payroll_item['lbp_payroll_account'] = $payroll_item['net_amount'];
        $payroll_item['salary'] = number_format(round($net / 2, 2), 2, '.', '');

        // Update change tracking
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

        // Recompute overall totals from ALL sections and employees
        $overallNet = 0;
        foreach ($this->records['payroll_items'] as $section) {
            foreach ($section['employees'] as $employee) {
                $overallNet += floatval($employee['net_amount'] ?? 0);
            }
        }

        $payroll['overall_net_amount'] = number_format($overallNet, 2, '.', '');
        $payroll['overall_salary_amount'] = number_format(round($overallNet / 2, 2), 2, '.', '');

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

                    $payrollItem = PayrollItems::find($employeeData['id']);
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
