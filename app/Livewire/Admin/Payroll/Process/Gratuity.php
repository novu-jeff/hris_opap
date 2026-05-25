<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\GratuityService;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\PayrollGratuityItems;
use App\Models\PayrollGratuity;
use App\Models\OtherEarnings;
use App\Models\Positions;
use App\Models\Tranche;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Gratuity extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;

    
    public $gratuity_pay = [];
    public $tax = [];
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

    $service = app(GratuityService::class);

    $records = $service->getPayroll(
        $this->payroll_id
    );

    $this->records = $records;

    $employmentType =
        $records['payroll']['employment_type']
        ?? null;

    $this->employment_type = strtolower(
        $employmentType['name'] ?? ''
    );

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE FIELDS
    |--------------------------------------------------------------------------
    */

    $totalGratuity = 0;
    $totalTax = 0;
    $totalNet = 0;

    foreach (
        $this->records['payroll_items']
        as $sectionIndex => $sectionGroup
    ) {

        $employees =
            $sectionGroup['employees']
            ?? [];

        foreach (
            $employees
            as $employeeIndex => $record
        ) {

            $gratuityPay = round(
                floatval(
                    $record['gratuity_pay'] ?? 0
                ),
                2
            );

            $tax = round(
                floatval(
                    $record['tax'] ?? 0
                ),
                2
            );

            $net = round(
                floatval(
                    $record['net_amount'] ?? 0
                ),
                2
            );

            /*
            |--------------------------------------------------------------------------
            | BINDINGS
            |--------------------------------------------------------------------------
            */

            $this->gratuity_pay[$sectionIndex][$employeeIndex]
                = $gratuityPay;

            $this->tax[$sectionIndex][$employeeIndex]
                = $tax;

            $this->net_amount[$sectionIndex][$employeeIndex]
                = $net;

            /*
            |--------------------------------------------------------------------------
            | TOTALS
            |--------------------------------------------------------------------------
            */

            $totalGratuity += $gratuityPay;

            $totalTax += $tax;

            $totalNet += $net;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PAYROLL TOTALS
    |--------------------------------------------------------------------------
    */

   // dd($totalGratuity );

    $this->records['payroll']['total_gratuity_pay']
    = $totalGratuity ?? 0;

$this->records['payroll']['total_tax']
    = $totalTax ?? 0;

$this->records['payroll']['total_net_amount']
    = $totalNet ?? 0;

    /*
    |--------------------------------------------------------------------------
    | ORIGINAL ITEMS
    |--------------------------------------------------------------------------
    */

    $this->originalItems = json_decode(
        json_encode(
            $records['payroll_items']
        ),
        true
    );

    /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

    if (
        is_null(
            $records['payroll']['batch_id']
        )
    ) {

        return redirect()->route(
            'payroll.index'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $this->isApproved =
        $records['payroll']['status']
        == 'approved';

    $this->records = $records;

    $this->batchId =
        $records['batch_id'];
}

public function recompute(
    $sectionIndex,
    $employeeIndex
) {

    $payroll_item =
        &$this->records['payroll_items']
            [$sectionIndex]['employees']
            [$employeeIndex];

    /*
    |--------------------------------------------------------------------------
    | GRATUITY PAY
    |--------------------------------------------------------------------------
    */

    $gratuityPay = round(
        floatval(
            $this->gratuity_pay
                [$sectionIndex]
                [$employeeIndex]
                ?? 0
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | TAX = 5%
    |--------------------------------------------------------------------------
    */

    $tax = round(
        $gratuityPay * 0.05,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | NET AMOUNT
    |--------------------------------------------------------------------------
    */

    $net = round(
        $gratuityPay - $tax,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | UPDATE RECORD
    |--------------------------------------------------------------------------
    */

    $payroll_item['gratuity_pay']
        = $gratuityPay;

    $payroll_item['tax']
        = $tax;

    $payroll_item['net_amount']
        = $net;

    /*
    |--------------------------------------------------------------------------
    | UPDATE BINDINGS
    |--------------------------------------------------------------------------
    */

    $this->gratuity_pay
        [$sectionIndex]
        [$employeeIndex]
        = $gratuityPay;

    $this->tax
        [$sectionIndex]
        [$employeeIndex]
        = $tax;

    $this->net_amount
        [$sectionIndex]
        [$employeeIndex]
        = $net;

    /*
    |--------------------------------------------------------------------------
    | RECOMPUTE TOTALS
    |--------------------------------------------------------------------------
    */

    $totalGratuity = 0;
    $totalTax = 0;
    $totalNet = 0;

    foreach (
        $this->records['payroll_items']
        as $section
    ) {

        foreach (
            $section['employees']
            as $employee
        ) {

            $totalGratuity += floatval(
                $employee['gratuity_pay']
                ?? 0
            );

            $totalTax += floatval(
                $employee['tax']
                ?? 0
            );

            $totalNet += floatval(
                $employee['net_amount']
                ?? 0
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SUMMARY
    |--------------------------------------------------------------------------
    */

    $this->records['payroll']
        ['total_gratuity_pay']
        = number_format(
            $totalGratuity,
            2,
            '.',
            ''
        );

    $this->records['payroll']
        ['total_tax']
        = number_format(
            $totalTax,
            2,
            '.',
            ''
        );

    $this->records['payroll']
        ['total_net_amount']
        = number_format(
            $totalNet,
            2,
            '.',
            ''
        );

    /*
    |--------------------------------------------------------------------------
    | TRACK CHANGES
    |--------------------------------------------------------------------------
    */

    $original =
        $this->originalItems
            [$sectionIndex]['employees']
            [$employeeIndex]
            ?? [];

    if (
        $this->isChanged(
            $payroll_item,
            $original
        )
    ) {

        $this->updatedItems[] =
            $payroll_item['id'];

        $this->updatedItems =
            array_unique(
                $this->updatedItems
            );

    } else {

        $this->updatedItems =
            array_diff(
                $this->updatedItems,
                [$payroll_item['id']]
            );
    }

    $this->hasChanges =
        count($this->updatedItems) > 0;
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
        'gratuity_pay',
        'tax',
        'net_amount'
    ];

    $employee = $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

    DB::transaction(function () use ($employee, $sectionIndex, $employeeIndex, $fields) {

        PayrollGratuityItems::where('id', $employee['id'])->delete();

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
    $payroll = PayrollGratuity::find($this->payroll_id);

    $emp = DB::table('employee_information as ei')

        ->leftJoin(
            'employee_personal as ep',
            'ei.employee_no',
            '=',
            'ep.employee_no'
        )

        ->where('ei.id', $id)

        ->select(

            'ei.employee_no',
            'ei.salary',
            'ei.position_id',
            'ei.section_id',
            'ei.date_hired',
            'ei.employment_type_id',

            DB::raw("
                CONCAT(
                    COALESCE(ep.firstname,''),
                    ' ',
                    COALESCE(ep.lastname,'')
                ) as name
            ")

        )

        ->first();

    if (!$emp) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | RESET WARNING
    |--------------------------------------------------------------------------
    */

    $this->showDuploicateLabel = false;

    /*
    |--------------------------------------------------------------------------
    | GET GRATUITY EARNING
    |--------------------------------------------------------------------------
    */

    $other_service = new \App\Http\Controllers\Admin\Services\OtherServices;

    $earnings = $other_service->earnings(
        $emp->employee_no
    );

    $gratuityPay = round(

        floatval(

            collect($earnings)

                ->firstWhere(
                    'code',
                    'GRATUITY'
                )['amount']

                ?? 0

        ),

        2

    );

    /*
    |--------------------------------------------------------------------------
    | TAX
    |--------------------------------------------------------------------------
    */

    $tax = round(
        $gratuityPay * 0.05,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | NET
    |--------------------------------------------------------------------------
    */

    $net = round(
        $gratuityPay - $tax,
        2
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

        'employment_type_id' =>

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

        'basic_salary' =>

            round(
                floatval($emp->salary ?? 0),
                2
            ),

        'date_hired' =>

            $emp->date_hired,

        'gratuity_pay' =>

            $gratuityPay,

        'tax' =>

            $tax,

        'net_amount' =>

            $net,

        'remarks' => null,

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

        $exist = PayrollGratuityItems::where('payroll_id', $this->payroll_id)
        ->where('employee_no', $this->selectedEmployee['employee_no'])
        ->exists();
       // dd('duplicatesss');
    if ($exist) {

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
    $payroll = PayrollGratuity::find($this->payroll_id);
    $type = $payroll->bonus_type; // mid_year or year_end

  
        $this->hasChanges = true;
        DB::transaction(function () {

            $positionName = $this->getPositionName($this->selectedEmployee['position_id']);
            $sectionName  = $this->getSectionName($this->selectedEmployee['section_id']);

            $new = PayrollGratuityItems::create([

                'payroll_id' => $this->payroll_id,
            
                'employee_no' =>
                    $this->selectedEmployee['employee_no'],
            
                'employment_type_id' =>
                    $this->selectedEmployee['employment_type_id'],
            
                'name' =>
                    $this->selectedEmployee['name'],
            
                'position' =>
                    $positionName,
            
                'basic_salary' =>
                    $this->selectedEmployee['basic_salary'],
            
                'remarks' =>
                    $this->selectedEmployee['remarks'] ?? null,
            
                'date_hired' =>
                    $this->selectedEmployee['date_hired'],
            
                'gratuity_pay' =>
                    $this->selectedEmployee['gratuity_pay'] ?? 0,
            
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

                'gratuity_pay',
                'tax',
                'net_amount',
            
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

        /*
        |--------------------------------------------------------------------------
        | PAYROLL TOTALS
        |--------------------------------------------------------------------------
        */

        $totalGratuity = 0;
        $totalTax = 0;
        $totalNet = 0;

        /*
        |--------------------------------------------------------------------------
        | UPDATE ITEMS
        |--------------------------------------------------------------------------
        */

        foreach ($this->records['payroll_items'] as $section) {

            foreach ($section['employees'] as $employeeData) {

                if (empty($employeeData['id'])) {
                    continue;
                }

                $payrollItem = PayrollGratuityItems::find(
                    $employeeData['id']
                );

                if (!$payrollItem) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | UPDATE DATA
                |--------------------------------------------------------------------------
                */

                $updateData = [

                    

                    'gratuity_pay' => $employeeData['gratuity_pay'] ?? 0,

                    'tax' => $employeeData['tax'] ?? 0,

                    'net_amount' => $employeeData['net_amount'] ?? 0,

                ];

                $payrollItem->update($updateData);

                /*
                |--------------------------------------------------------------------------
                | RECOMPUTE PAYROLL TOTALS
                |--------------------------------------------------------------------------
                */

                $totalGratuity += floatval(
                    $employeeData['gratuity_pay'] ?? 0
                );

                $totalTax += floatval(
                    $employeeData['tax'] ?? 0
                );

                $totalNet += floatval(
                    $employeeData['net_amount'] ?? 0
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE PAYROLL HEADER
        |--------------------------------------------------------------------------
        */

        PayrollGratuity::where('id', $this->payroll_id)
            ->update([

                'updated_at' => now()

            ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE LOCAL RECORDS
        |--------------------------------------------------------------------------
        */

        $this->records['payroll']['total_gratuity_pay']
            = number_format($totalGratuity, 2, '.', '');

        $this->records['payroll']['total_tax']
            = number_format($totalTax, 2, '.', '');

        $this->records['payroll']['total_net_amount']
            = number_format($totalNet, 2, '.', '');

        DB::commit();

        /*
        |--------------------------------------------------------------------------
        | RESET TRACKING
        |--------------------------------------------------------------------------
        */

        $this->newItems = [];

        $this->hasChanges = false;

        $this->updatedItems = [];

        /*
        |--------------------------------------------------------------------------
        | REFRESH ORIGINALS
        |--------------------------------------------------------------------------
        */

        $this->originalItems = json_decode(
            json_encode($this->records['payroll_items']),
            true
        );

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return $this->dispatch('alert', [

            'showAlert' => true,

            'status' => 'success',

            'title' => 'Success!',

            'message' => 'Premium payroll changes saved successfully.'

        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error(
            'Premium payroll save failed',
            [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        );

        return $this->dispatch('alert', [

            'showAlert' => true,

            'status' => 'error',

            'title' => 'Error!',

            'message' => $e->getMessage()

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
            $payroll = PayrollGratuity::find($this->payroll_id);
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
        return view('livewire.admin.payroll.process.gratuity');
    }
}
