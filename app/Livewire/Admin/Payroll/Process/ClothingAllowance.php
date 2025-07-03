<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\ClothingAllowanceService;
use App\Models\ClothingAllowanceItemsPayroll;
use App\Models\ClothingAllowancePayroll;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClothingAllowance extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;
    public $allowance = [];
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

        $service = app(ClothingAllowanceService::class);

        $records = $service->getPayroll($this->payroll_id);

        foreach ($records['payroll_items'] as $sectionIndex => $sectionGroup) {
            $employees = $sectionGroup['employees'] ?? [];

            foreach ($employees as $employeeIndex => $record) {
                $this->allowance[$sectionIndex][$employeeIndex]   = $record['allowance'] ?? 0;
            }
        }

        $this->originalItems = json_decode(json_encode($records['payroll_items']), true);

        if(is_null($records['payroll']['batch_id'])) {
            return redirect()->route('payroll.index');
        }

        $this->records = $records;

        $this->batchId = $records['batch_id'];

    }

    public function recompute($sectionIndex, $employeeIndex)
    {
        $payroll = &$this->records['payroll'];
        
        $payroll_item = &$this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

        $payroll_item['allowance']   = floatval($this->allowance[$sectionIndex][$employeeIndex] ?? 0);


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
                    $this->updatedItems = array_values($this->updatedItems);
                }
                $this->hasChanges = !empty($this->updatedItems);
            }
        }

        $overallSalary = 0;

        foreach ($this->records['payroll_items'] as $section) {
            foreach ($section['employees'] as $employee) {
                $overallSalary += floatval($employee['allowance'] ?? 0);
            }
        }

        $payroll['total_allowances'] = number_format($overallSalary, 2, '.', '');
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

                    $payrollItem = ClothingAllowanceItemsPayroll::find($employeeData['id']);
                    if (!$payrollItem) {
                        continue;
                    }

                    $updateData = [
                        'allowance' => $employeeData['allowance'] ?? 0,
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
            $payroll = ClothingAllowancePayroll::find($this->payroll_id);
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
        return view('livewire.admin.payroll.process.clothing-allowance');
    }
}
