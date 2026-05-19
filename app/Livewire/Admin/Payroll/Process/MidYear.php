<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\BonusService;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\BonusItemsPayroll;
use App\Models\BonusPayroll;
use App\Models\OtherEarnings;
use App\Models\Positions;
use App\Models\Tranche;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class MidYear extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;
    public $bonus = [];
    public $percentage = [];
    public $cash_gift = [];
    public $net_amount = [];
    public array $originalItems = [];
    public array $updatedItems = [];
    public bool $isApproved = false;
    public bool $hasChanges = false;
    public $records;

    public $confirmingDelete = false;
    public $deleteSectionIndex;
    public $deleteEmployeeIndex;

    public $showAddModal = false;
    public $searchEmployee = '';
    public $employeeResults = [];
    public $selectedEmployee = null;
    public $showDuploicateLabel = false;
    public $duplicateMessage = '';

    public array $newItems = [];

    protected $listeners = ['save', 'approve'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $this->product = config('app.product');

        $service = app(BonusService::class);

        $records = $service->getPayroll($this->payroll_id);

        $employmentType = $records['payroll']['employment_type'] ?? null;
        $this->employment_type = strtolower($employmentType['name'] ?? ''); 

        foreach ($records['payroll_items'] as $sectionIndex => $sectionGroup) {
            $employees = $sectionGroup['employees'] ?? [];

            foreach ($employees as $employeeIndex => $record) {
                $this->bonus[$sectionIndex][$employeeIndex]   = $record['bonus'] ?? 0;
                $this->cash_gift[$sectionIndex][$employeeIndex]   = $record['cash_gift'] ?? 0;
                $this->percentage[$sectionIndex][$employeeIndex]   = $record['percentage'] ?? 0;
                $this->net_amount[$sectionIndex][$employeeIndex]   = $record['net_amount'] ?? 0;
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

        $bonus     = floatval($this->bonus[$sectionIndex][$employeeIndex] ?? 0);
        $cash_gift = floatval($this->cash_gift[$sectionIndex][$employeeIndex] ?? 0);

        $payrollService = app(PayrollService::class);
        $tax = $payrollService->computeBonusTax($bonus, $cash_gift);
        $net = ($bonus + $cash_gift) - $tax;

        $payroll_item['bonus']      = $bonus;
        $payroll_item['cash_gift']  = $cash_gift;
        $payroll_item['tax']        = $tax;
        $payroll_item['net_amount'] = number_format($net, 2, '.', '');

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

        $total_bonus = 0;
        $total_cash_gift = 0;
        $total_tax = 0;
        $total_net_amount = 0;

        foreach ($this->records['payroll_items'] as $section) {
            foreach ($section['employees'] as $employee) {
                $total_bonus       += floatval($employee['bonus'] ?? 0);
                $total_cash_gift   += floatval($employee['cash_gift'] ?? 0);
                $total_tax         += floatval($employee['tax'] ?? 0);
                $total_net_amount  += floatval($employee['net_amount'] ?? 0);
            }
        }

        $payroll['total_bonus']            = number_format($total_bonus, 2, '.', '');
        $payroll['total_cash_gift_bonus']  = number_format($total_cash_gift, 2, '.', '');
        $payroll['total_tax']              = number_format($total_tax, 2, '.', '');
        $payroll['total_net_amount']       = number_format($total_net_amount, 2, '.', '');
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

    public function confirmDelete($sectionIndex, $employeeIndex)
{
    $this->deleteSectionIndex = $sectionIndex;
    $this->deleteEmployeeIndex = $employeeIndex;
    $this->confirmingDelete = true;

   /* $this->dispatch('showConfirmation', [
        'title' => 'Delete employee?',
        'message' => 'This will permanently remove this employee from payroll.',
        'action' => 'deleteEmployee'
    ]);*/
}

public function deleteEmployee()
{
    $sectionIndex = $this->deleteSectionIndex;
    $employeeIndex = $this->deleteEmployeeIndex;

    if ($this->isApproved) return;

    $fields = [
        'bonus',
        'percentage',
        'net_amount'
    ];

    $employee = $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

    DB::transaction(function () use ($employee, $sectionIndex, $employeeIndex, $fields) {

        BonusItemsPayroll::where('id', $employee['id'])->delete();

        unset($this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex]);

        // ✅ REMOVE FROM ALL FIELD ARRAYS
        foreach ($fields as $field) {
            Log::info('check items', ['field' =>  $field, 'sectionIndex' => $sectionIndex, 'employeeIndex' => $employeeIndex ]);
            unset($this->{$field}[$sectionIndex][$employeeIndex]);

            // reindex each field
            if (isset($this->{$field}[$sectionIndex])) {
                $this->{$field}[$sectionIndex] = array_values($this->{$field}[$sectionIndex]);
            }
        }
        
        $this->records['payroll_items'][$sectionIndex]['employees'] = array_values(
            $this->records['payroll_items'][$sectionIndex]['employees']
        );
    });
    $this->confirmingDelete = false;

    $this->dispatch('alert', [
        'status' => 'success',
        'title' => 'Deleted',
        'message' => 'Employee removed from payroll'
    ]);
}


public function searchEmployeeAction($value)
{
    $this->searchEmployee = $value;

    if (trim($value) === '') {
        $this->employeeResults = [];
        return;
    }

    $this->employeeResults = DB::table('employee_information as ei')
        ->leftJoin('employee_personal as ep', 'ei.employee_no', '=', 'ep.employee_no')
        ->where('ei.isDeleted', 0)
        ->where('ei.status', 'active')
        ->where('ei.employment_type_id', 1)
        ->where(function ($q) use ($value) {
            $q->where('ei.employee_no', 'like', '%' . $value . '%')
              ->orWhere('ep.firstname', 'like', '%' . $value . '%')
              ->orWhere('ep.lastname', 'like', '%' . $value . '%');
        })
        ->limit(10)
        ->select(
            'ei.id',
            'ei.employee_no',
            DB::raw("CONCAT(COALESCE(ep.firstname,''), ' ', COALESCE(ep.lastname,'')) as name")
        )
        ->get();

        $this->showDuploicateLabel = false;    
}

public function selectEmployee($id)
{
    $payroll = BonusPayroll::find($this->payroll_id);

    $emp = DB::table('employee_information as ei')
        ->leftJoin('employee_personal as ep', 'ei.employee_no', '=', 'ep.employee_no')
        ->where('ei.id', $id)
        ->select(
            'ei.employee_no',
            'ei.salary',
            'ei.position_id',
            'ei.section_id',
            'ei.date_hired',
            'ei.w_tax',
            'ei.tax_type',
            'ei.step_id',
            'ei.salary_type',
            'ei.employment_type_id',
            'ep.bp_no',
            DB::raw("CONCAT(ep.firstname, ' ', ep.lastname) as name")
        )
        ->first();

    if (!$emp) {
        return;
    }

    // reset duplicate warning
    $this->showDuploicateLabel = false;

    $stepId = $emp->step_id;
    $eligible = $emp->employment_type_id;


        if ($emp->employment_type_id != 3 && $emp->employment_type_id != 4) {


            $salaryGrade = Positions::where('id', $emp->position_id)->value('salary_grade');

            $stepColumn = "step_" . ($stepId  ?? '');
             $stepColumnTax = "step_" . ($stepId  ?? '') . "_wtax";

             // Get the latest tranche for this eligible type
             $latestTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn, $stepColumnTax) {
                 $query->where('salary_grade', $salaryGrade)
                  ->select('id', 'tranche_id', 'salary_grade', $stepColumn, $stepColumnTax);
             }])
              ->where('eligible', $eligible)
             ->where('is_active', 1)
             ->latest('year')
             ->first();
             

             $salary = ($latestTranche && $latestTranche->items->isNotEmpty()) 
                     ? $latestTranche->items->first()->$stepColumn 
                     : 0;
                 
             $wtax = ($latestTranche && $latestTranche->items->isNotEmpty()) 
                 ? $latestTranche->items->first()->$stepColumnTax 
                 : 0;

        } else {
        // dd('here');
            $wtax = data_get($emp, 'w_tax', 0);
            $salary = data_get($emp, 'salary', 0);
        } 

    $employee_salary = round(floatval($salary), 2);

    $cash_gift = 0;

    if($payroll->bonus_type == 'year_end') {
        $cash_gift = OtherEarnings::where('code', 'cashgift')->value('amount') ?? 0;
    }
    
    $bonus = $employee_salary;
    if($payroll->bonus_type == 'year_end') {
        $tax = $this->payrollService->computeBonusTax($bonus, $cash_gift);
        $net = ($bonus + $cash_gift) - $tax;
    }else{
        $tax = 0;
        $net = $bonus;
    }

    $this->selectedEmployee = [
        'payroll_id' => $payroll->id,
        'employee_no' => $emp->employee_no,
        'employment_type' => $emp->employment_type_id,
        'position_id' => $emp->position_id,
        'section_id' => $emp->section_id,

        'name' => $emp->name,
        'position' => $this->getPositionName($emp->position_id),

        'date_hired' => $emp->date_hired,

        'basic_salary' => $employee_salary,
        'bonus' => $employee_salary,
        'cash_gift' => $cash_gift,
        'percentage' => $payroll->percentage,
        'coverage_from' => $payroll->coverage_from,
        'coverage_to' => $payroll->coverage_to,
        'tax' => $tax,
        'net_amount' => $net 
    ];

    // optional: clear results after select
    $this->employeeResults = [];
}

    public function confirmAddEmployee()
    {
        if (!$this->selectedEmployee) return;

        $existss = BonusItemsPayroll::where('payroll_id', $this->payroll_id)
        ->where('employee_no', $this->selectedEmployee['employee_no'])
        ->exists();
       // dd('duplicatesss');
    if ($existss) {

      //  dd('duplicate');
        $this->showDuploicateLabel = true;
        $this->duplicateMessage = 'Employee already exists in this payroll';

        $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Duplicate',
            'message' => 'Employee already exists in payrollvvv'
        ]);
        return;
    } 
        /*
    |--------------------------------------------------------------------------
    | MID YEAR VALIDATION
    |--------------------------------------------------------------------------
    */
    $payroll = BonusPayroll::find($this->payroll_id);
    $type = $payroll->bonus_type; // mid_year or year_end

    if ($type === 'mid_year') {
       // dd('enter');
        $dateHired = !empty($this->selectedEmployee['date_hired'])
            ? \Carbon\Carbon::parse($this->selectedEmployee['date_hired'])
            : null;

        $currentYear = now()->year;

        $may15 = \Carbon\Carbon::create($currentYear, 5, 15);
        $july1Prev = \Carbon\Carbon::create($currentYear - 1, 7, 1);

        $reasons = [];

        if (!$dateHired) {
          //  dd('no date hired');
            $reasons[] = 'No date hired';
        }

        if ($dateHired && $dateHired->gt($may15)) {
            $reasons[] = 'Not in service as of May 15';
        }

        if ($dateHired && $dateHired->gt($july1Prev)) {
            if ($dateHired->diffInMonths($may15) < 4) {
                $reasons[] = 'Less than 4 months of service from July 1 to May 15';
            }
        }

        if (!empty($reasons)) {
            $this->showDuploicateLabel = true;
            $this->duplicateMessage = implode(', ', $reasons);

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Employee Not Eligible',
                'message' => $this->duplicateMessage
            ]);

            return;
        }
    }
        $this->hasChanges = true;
        DB::transaction(function () {

            $positionName = $this->getPositionName($this->selectedEmployee['position_id']);
            $sectionName  = $this->getSectionName($this->selectedEmployee['section_id']);

            $new = BonusItemsPayroll::create([
                'payroll_id' => $this->payroll_id,
                'employee_no' => $this->selectedEmployee['employee_no'],
                'employment_type_id' => $this->selectedEmployee['employment_type'],
                'name' => $this->selectedEmployee['name'],
                'position' => $positionName,
                'basic_salary' => $this->selectedEmployee['basic_salary'],
                'percentage' => $this->selectedEmployee['percentage'],
                'bonus' => $this->selectedEmployee['bonus'],
                'cash_gift' => $this->selectedEmployee['cash_gift'],
                'date_hired' => $this->selectedEmployee['date_hired'],
                'coverage_from' => $this->selectedEmployee['coverage_from'],
                'coverage_to' => $this->selectedEmployee['coverage_to'],
                'tax' => $this->selectedEmployee['tax'],
                'net_amount' => $this->selectedEmployee['net_amount'],
            ]);

        // $this->loadRecords();

            $newItem = $new->toArray();

        
            \Log::info('Add employee to Mid-year', ['records' => $newItem]);

            $sectionIndex = $this->findOrCreateSection([
                'section_name' => $sectionName
            ]);

            $this->records['payroll_items'][$sectionIndex]['employees'][] = $newItem;

        $employeeIndex = count($this->records['payroll_items'][$sectionIndex]['employees']) - 1;

            // init fields
            foreach ([
                'percentage',
                'bonus','net_amount'
            ] as $field) {
                
                $this->{$field}[$sectionIndex][$employeeIndex] = $newItem[$field] ?? 0;
            }

            $payrollItem = &$this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];
            $original    = $this->originalItems[$sectionIndex]['employees'][$employeeIndex] ?? [];

            if ($this->isChanged($payrollItem, $original)) {
                Log::info('new employee haschange1', ['payrollItem' => $payrollItem,'original' => $original]);
                $this->updatedItems[] = $payrollItem['id'];
                $this->updatedItems = array_unique($this->updatedItems);
            } else {
                Log::info('new employee haschange2', ['payrollItem' => $payrollItem,'original' => $original]);
                $this->updatedItems = array_diff($this->updatedItems, [$payrollItem['id']]);
            }

        // $this->recompute($sectionIndex, $employeeIndex);
            $this->newItems[] = $new->id;
            $this->hasChanges = true;
        });

        $this->reset(['selectedEmployee', 'searchEmployee', 'employeeResults', 'showAddModal', 'showDuploicateLabel']);
        $this->hasChanges = true;
        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Added',
            'message' => 'Employee added to Mid-Year payroll'
        ]);
    }

    private function getPositionName($positionId)
    {
        return DB::table('positions')
            ->where('id', $positionId)
            ->value('name') ?? 'N/A';
    }

    private function getSectionName($sectionId)
    {
        return DB::table('sections')
            ->where('id', $sectionId)
            ->value('name') ?? 'Unknown Section';
    }

    private function findOrCreateSection($data)
{
    $sectionName = $data['section_name'] ?? 'Unknown Section';

    // 1. Try to find existing section
    foreach ($this->records['payroll_items'] as $index => $section) {
        if (($section['section_name'] ?? '') === $sectionName) {
            return $index;
        }
    }

    // 2. Create new section if not found
    $this->records['payroll_items'][] = [
        'section_name' => $sectionName,
        'employees' => []
    ];

    return count($this->records['payroll_items']) - 1;
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

                    $payrollItem = BonusItemsPayroll::find($employeeData['id']);
                    if (!$payrollItem) {
                        continue;
                    }

                    $updateData = [
                        'bonus' => $employeeData['bonus'] ?? 0,
                        'percentage' => $employeeData['percentage'] ?? 0,
                        'tax' => $employeeData['tax'] ?? 0,
                        'net_amount' => $employeeData['net_amount'] ?? 0,
                    ];

                    $payrollItem->update($updateData);
                }
            }

            DB::commit();

            $this->newItems = [];
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
            $payroll = BonusPayroll::find($this->payroll_id);
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
        return view('livewire.admin.payroll.process.mid-year');
    }
}
