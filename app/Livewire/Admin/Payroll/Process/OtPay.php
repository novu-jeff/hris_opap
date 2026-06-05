<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\OverTimeService;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Services\DailyTimeRecordService;
use App\Models\OTPayroll;
use App\Models\OTItemsPayroll;
use App\Models\Positions;
use App\Models\Tranche;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public $payroll_service;
    

    public $confirmingDelete = false;
    public $deleteSectionIndex;
    public $deleteEmployeeIndex;

    public $showAddModal = false;
    public $searchEmployee = '';
    public $employeeResults = [];
    public $selectedEmployee = null;
    public $showDuploicateLabel = false;
    public $duplicateMessage = '';

    public $basic_salary = [];
    public $duration = [];
    public $tax = [];
    public $net_amount = [];

    public array $newItems = [];

    protected $listeners = [
        'save',
        'approve',
        'deleteEmployee',
        'confirmSave',
        'recompute'
    ];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $this->product = config('app.product');
        $service = app(OverTimeService::class);

        $records = $service->getPayroll($this->payroll_id);

        $employmentType = $records['payroll']['employment_type'] ?? null;
        $this->employment_type = strtolower($employmentType['name'] ?? ''); 

        foreach ($records['payroll_items'] as $sectionIndex => $sectionGroup) {

            $employees = $sectionGroup['employees'] ?? [];
        
            foreach ($employees as $employeeIndex => $record) {
        
                $this->basic_salary[$sectionIndex][$employeeIndex] =
                    $record['basic_salary'] ?? 0;
        
                $minutes = $record['duration'] ?? 0;

                $hours = floor($minutes / 60);
                $mins = $minutes % 60;
                
                $this->duration[$sectionIndex][$employeeIndex] =
                    sprintf('%02d:%02d', $hours, $mins);
        
                $this->amount[$sectionIndex][$employeeIndex] =
                    $record['amount'] ?? 0;
        
                $this->tax[$sectionIndex][$employeeIndex] =
                    $record['tax'] ?? 0;
        
                $this->net_amount[$sectionIndex][$employeeIndex] =
                    $record['net_amount'] ?? 0;
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
        $basicSalary = (float) (
            $this->basic_salary[$sectionIndex][$employeeIndex] ?? 0
        );

        $durationInput =
            $this->duration[$sectionIndex][$employeeIndex] ?? '00:00';

        // Parse HH:MM
        if (str_contains($durationInput, ':')) {

            [$hours, $minutes] = explode(':', $durationInput);

            $hours = (int) $hours;
            $minutes = (int) $minutes;

            $timeInMinutes = ($hours * 60) + $minutes;

        } else {

            $timeInMinutes = 0;
        }

        $decimalHours = $timeInMinutes / 60;

        $dailyRate = $basicSalary / 22;

        $hourlyRate = $dailyRate / 8;

        $otRate = $hourlyRate * 1.25;

        $grossOtPay = $decimalHours * $otRate;

       // $tax = 0; // your tax formula
        $tax = (float) (
            $this->tax[$sectionIndex][$employeeIndex] ?? 0
        );

        $netAmount = $grossOtPay - $tax;

        $this->amount[$sectionIndex][$employeeIndex] = round(floatval($grossOtPay ?? 0), 2);
   

    $this->tax[$sectionIndex][$employeeIndex] = round(floatval($tax ?? 0), 2);
       

    $this->net_amount[$sectionIndex][$employeeIndex] = round(floatval($netAmount ?? 0), 2);
        

        $employeeId =
    $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex]['id']
    ?? null;

    if ($employeeId) {

        if (!in_array($employeeId, $this->updatedItems)) {

            $this->updatedItems[] = $employeeId;
        }
    }

        $this->hasChanges = true;
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
                'redirect' => route('payroll.process', ['type' => $this->type, 'payroll_id' => $this->payroll_id])
            ]);
        }
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
    }

    public function deleteEmployee()
    {
        if ($this->isApproved) return;

        $sectionIndex = $this->deleteSectionIndex;
        $employeeIndex = $this->deleteEmployeeIndex;

        $employee = $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

        DB::transaction(function () use ($employee, $sectionIndex, $employeeIndex) {

            OTItemsPayroll::where('id', $employee['id'])->delete();

            unset($this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex]);

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
        ->whereIn('ei.employment_type_id', [2, 3, 4])
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
    $payroll = OTPayroll::find($this->payroll_id);
    $dtr_service = app(DailyTimeRecordService::class);
    $payrollService = app(PayrollService::class);



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

    /*
    |--------------------------------------------------------------------------
    | RESET DUPLICATE WARNING
    |--------------------------------------------------------------------------
    */

    $this->showDuploicateLabel = false;

    /*
    |--------------------------------------------------------------------------
    | GET SALARY
    |--------------------------------------------------------------------------
    */

    $stepId = $emp->step_id;
    $eligible = $emp->employment_type_id;

    if (
        $emp->employment_type_id != 3
        && $emp->employment_type_id != 4
    ) {

        $salaryGrade = Positions::where(
            'id',
            $emp->position_id
        )->value('salary_grade');

        $stepColumn = "step_" . ($stepId ?? '');

        $stepColumnTax =
            "step_" . ($stepId ?? '') . "_wtax";

        $latestTranche = Tranche::with([
            'items' => function ($query)
            use (
                $salaryGrade,
                $stepColumn,
                $stepColumnTax
            ) {

                $query->where(
                    'salary_grade',
                    $salaryGrade
                )

                ->select(
                    'id',
                    'tranche_id',
                    'salary_grade',
                    $stepColumn,
                    $stepColumnTax
                );
            }
        ])

        ->where('eligible', $eligible)

        ->where('is_active', 1)

        ->latest('year')

        ->first();

        $salary = (
            $latestTranche
            && $latestTranche->items->isNotEmpty()
        )

        ? $latestTranche
            ->items
            ->first()
            ->$stepColumn

        : 0;

        $wtax = (
            $latestTranche
            && $latestTranche->items->isNotEmpty()
        )

        ? $latestTranche
            ->items
            ->first()
            ->$stepColumnTax

        : 0;

    } else {

        $wtax = data_get($emp, 'w_tax', 0);

        $salary = data_get($emp, 'salary', 0);
    }

    $basicSalary = round(
        floatval($salary),
        2
    );

    [$start, $end] = explode(' to ', $payroll->period);
    
    $dtr = $dtr_service->getDailyTimeRecord($emp->employee_no,  [trim($start), trim($end)], true);
    
    $totalDays = $dtr['summary']['total_days'] ?? 0;
    $workedDays = $dtr['summary']['worked_days'] ?? 0;
    $overtime = $dtr['summary']['overtime_minues'] ?? 0;

    $ot = $payrollService->computeOvertimePay(
        $basicSalary,
        $workedDays,
        $overtime
    );
    /*
    |--------------------------------------------------------------------------
    | SET SELECTED EMPLOYEE
    |--------------------------------------------------------------------------
    */

    $this->selectedEmployee = [

        'payroll_id' =>
            $payroll->id,

        'employee_no' =>
            $emp->employee_no,

        'employment_type' =>
            $emp->employment_type_id,

        'position_id' =>
            $emp->position_id,

        'section_id' =>
            $emp->section_id,

        'name' =>
            strtoupper($emp->name),

        'position' =>
            strtoupper(
                $this->getPositionName(
                    $emp->position_id
                )
            ),

        'date_hired' =>
            $emp->date_hired,

        'basic_salary' =>
            $basicSalary,

        'duration'      => $overtime,
        'amount'        => $ot['gross_ot_pay'],
        'tax'           => $ot['tax'],
        'net_amount'    => $ot['net_ot_pay'],

    ];

    /*
    |--------------------------------------------------------------------------
    | CLEAR SEARCH RESULTS
    |--------------------------------------------------------------------------
    */

    $this->employeeResults = [];
}

    public function confirmAddEmployee()
    {
        if (!$this->selectedEmployee) return;

        $existss = OTItemsPayroll::where('payroll_id', $this->payroll_id)
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
 
   
        $this->hasChanges = true;
        DB::transaction(function () {

            $positionName = $this->getPositionName($this->selectedEmployee['position_id']);
            $sectionName  = $this->getSectionName($this->selectedEmployee['section_id']);

            $new = OTItemsPayroll::create([

                'payroll_id' =>
                    $this->payroll_id,
            
                'employee_no' =>
                    $this->selectedEmployee['employee_no'],
            
                'name' =>
                    $this->selectedEmployee['name'],
            
                'position' =>
                    $positionName,
            
                'basic_salary' =>
                    $this->selectedEmployee['basic_salary'],
            
                'duration' =>
                    $this->selectedEmployee['duration'] ?? 0,
            
                'amount' =>
                    $this->selectedEmployee['amount'] ?? 0,
            
                'tax' =>
                    $this->selectedEmployee['tax'] ?? 0,
            
                'net_amount' =>
                    $this->selectedEmployee['net_amount'] ?? 0,
            
               
            
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

                'duration',
                'amount',
                'tax',
                'net_amount',
                'basic_salary',
            
            ] as $field){
                
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
        $this->loadRecords();
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

            $totalAmount = 0;
            $totalTax = 0;
            $totalNet = 0;

            foreach ($this->records['payroll_items'] as $sectionIndex => $section) {

                foreach ($section['employees'] as $employeeIndex => $employeeData) {
            
                    $item = OTItemsPayroll::find(
                        $employeeData['id']
                    );
            
                    if (!$item) {
                        continue;
                    }
            
                    $item->update([
                        'basic_salary' => (float) (
                            $this->basic_salary[$sectionIndex][$employeeIndex] ?? 0
                        ),
            
                        'duration' => $this->convertDurationToMinutes(
                            $this->duration[$sectionIndex][$employeeIndex] ?? '00:00'
                        ),
            
                        'amount' => (float) (
                            $this->amount[$sectionIndex][$employeeIndex] ?? 0
                        ),
            
                        'tax' => (float) (
                            $this->tax[$sectionIndex][$employeeIndex] ?? 0
                        ),
            
                        'net_amount' => (float) (
                            $this->net_amount[$sectionIndex][$employeeIndex] ?? 0
                        ),
                    ]);
                }
            }

            OTPayroll::where('id', $this->payroll_id)
                ->update([
                    'updated_at' => now()
                ]);

            DB::commit();

            $this->loadRecords();

            $this->hasChanges = false;
            $this->updatedItems = [];

            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success!',
                'message' => 'OT payroll changes saved successfully.'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('OT Payroll Save Failed', [
                'message' => $e->getMessage()
            ]);

            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error!',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function convertDurationToMinutes($duration): int
    {
        if (!str_contains($duration, ':')) {
            return 0;
        }

        [$hours, $minutes] = explode(':', $duration);

        return ((int)$hours * 60) + (int)$minutes;
    }


    public function render()
    {
        return view('livewire.admin.payroll.process.ot-pay');
    }
}
