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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Premium extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;

    public $january_amount = [];
    public $february_amount = [];
    public $march_amount = [];
    public $april_amount = [];
    public $may_amount = [];
    public $june_amount = [];
    public $july_amount = [];
    public $august_amount = [];
    public $september_amount = [];
    public $october_amount  = [];
    public $november_amount  = [];
    public $december_amount  = [];
    public $total_amount = [];
    public $tax = [];
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

        $this->records = $records;

        $employmentType = $records['payroll']['employment_type'] ?? null;
        $this->employment_type = strtolower($employmentType['name'] ?? ''); 

        foreach ($this->records['payroll_items'] as $sectionIndex => $sectionGroup) {
            $employees = $sectionGroup['employees'] ?? [];

            foreach ($employees as $employeeIndex => $record) {
                $this->january_amount[$sectionIndex][$employeeIndex] = $record['january_amount'] ?? 0;
                $this->february_amount[$sectionIndex][$employeeIndex] = $record['february_amount'] ?? 0;
                $this->march_amount[$sectionIndex][$employeeIndex] = $record['march_amount'] ?? 0;
                $this->april_amount[$sectionIndex][$employeeIndex] = $record['april_amount'] ?? 0;
                $this->may_amount[$sectionIndex][$employeeIndex] = $record['may_amount'] ?? 0;
                $this->june_amount[$sectionIndex][$employeeIndex] = $record['june_amount'] ?? 0;
                $this->july_amount[$sectionIndex][$employeeIndex] = $record['july_amount'] ?? 0;
                $this->august_amount[$sectionIndex][$employeeIndex] = $record['august_amount'] ?? 0;
                $this->september_amount[$sectionIndex][$employeeIndex] = $record['september_amount'] ?? 0;
                $this->october_amount[$sectionIndex][$employeeIndex] = $record['october_amount'] ?? 0;
                $this->november_amount[$sectionIndex][$employeeIndex] = $record['november_amount'] ?? 0;
                $this->december_amount[$sectionIndex][$employeeIndex] = $record['december_amount'] ?? 0;
                $this->tax[$sectionIndex][$employeeIndex] = $record['tax'] ?? 0;
                $this->total_amount[$sectionIndex][$employeeIndex] = $record['total_amount'] ?? 0;
                $this->bonus[$sectionIndex][$employeeIndex] = $record['bonus'] ?? 0;
                $this->cash_gift[$sectionIndex][$employeeIndex] = $record['cash_gift'] ?? 0;
                $this->percentage[$sectionIndex][$employeeIndex] = $record['percentage'] ?? 0;
                $this->net_amount[$sectionIndex][$employeeIndex] = $record['net_amount'] ?? 0;
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

        /*
        |--------------------------------------------------------------------------
        | MONTHS
        |--------------------------------------------------------------------------
        */

        $months = [

            'january_amount',
            'february_amount',
            'march_amount',
            'april_amount',
            'may_amount',
            'june_amount',

            'july_amount',
            'august_amount',
            'september_amount',
            'october_amount',
            'november_amount',
            'december_amount',

        ];

        /*
        |--------------------------------------------------------------------------
        | UPDATE MONTH VALUES FROM INPUT
        |--------------------------------------------------------------------------
        */

        foreach ($months as $month) {

            if (
                isset($this->{$month}[$sectionIndex][$employeeIndex])
            ) {

                $payroll_item[$month] = round(
                    floatval(
                        $this->{$month}[$sectionIndex][$employeeIndex]
                    ),
                    2
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | SEMESTER TOTAL
        |--------------------------------------------------------------------------
        */

        $semester = $payroll['semester'] ?? null;

        $totalAmount = 0;

        if ($semester === 'first_semester') {

            $totalAmount += floatval($payroll_item['january_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['february_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['march_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['april_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['may_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['june_amount'] ?? 0);

        }

        if ($semester === 'second_semester') {

            $totalAmount += floatval($payroll_item['july_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['august_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['september_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['october_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['november_amount'] ?? 0);
            $totalAmount += floatval($payroll_item['december_amount'] ?? 0);

        }

        /*
        |--------------------------------------------------------------------------
        | PERCENTAGE
        |--------------------------------------------------------------------------
        */

        $percentage = round(
            floatval(
                $this->percentage[$sectionIndex][$employeeIndex] ?? 100
            ),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | BONUS
        |--------------------------------------------------------------------------
        */

        $bonus = round(
            $totalAmount * ($percentage / 100),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | TAX
        |--------------------------------------------------------------------------
        */

        $tax = round(
            floatval(
                $this->tax[$sectionIndex][$employeeIndex] ?? 0
            ),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | NET
        |--------------------------------------------------------------------------
        */

        $net = round(
            $bonus - $tax,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | UPDATE RECORD
        |--------------------------------------------------------------------------
        */

        $payroll_item['total_amount'] = number_format($totalAmount, 2, '.', '');

        $payroll_item['percentage'] = $percentage;

        $payroll_item['bonus'] = $bonus;

        $payroll_item['tax'] = $tax;

        $payroll_item['net_amount'] = $net;

        /*
        |--------------------------------------------------------------------------
        | UPDATE BINDINGS
        |--------------------------------------------------------------------------
        */

        $this->total_amount[$sectionIndex][$employeeIndex] = number_format($totalAmount, 2, '.', '');

        $this->bonus[$sectionIndex][$employeeIndex] = $bonus;

        $this->tax[$sectionIndex][$employeeIndex] = $tax;

        $this->net_amount[$sectionIndex][$employeeIndex] = $net;

        /*
        |--------------------------------------------------------------------------
        | TRACK CHANGES
        |--------------------------------------------------------------------------
        */

        $original =
            $this->originalItems[$sectionIndex]['employees'][$employeeIndex]
            ?? null;

        if ($original) {

            $hasChanged = $this->isChanged(
                $payroll_item,
                $original
            );

            $item_id = $payroll_item['id'];

            if ($hasChanged) {

                if (!in_array($item_id, $this->updatedItems)) {

                    $this->updatedItems[] = $item_id;

                }

                $this->hasChanges = true;

            } else {

                $key = array_search(
                    $item_id,
                    $this->updatedItems
                );

                if ($key !== false) {

                    unset($this->updatedItems[$key]);

                    $this->updatedItems =
                        array_values($this->updatedItems);

                }

                $this->hasChanges =
                    !empty($this->updatedItems);

            }
        }

        /*
        |--------------------------------------------------------------------------
        | RECOMPUTE PAYROLL TOTALS
        |--------------------------------------------------------------------------
        */

        $total_bonus = 0;
        $total_tax = 0;
        $total_net_amount = 0;

        foreach ($this->records['payroll_items'] as $section) {

            foreach ($section['employees'] as $employee) {

                $total_bonus +=
                    floatval($employee['bonus'] ?? 0);

                $total_tax +=
                    floatval($employee['tax'] ?? 0);

                $total_net_amount +=
                    floatval($employee['net_amount'] ?? 0);

            }

        }

        $payroll['total_bonus'] =
            number_format($total_bonus, 2, '.', '');

        $payroll['total_tax'] =
            number_format($total_tax, 2, '.', '');

        $payroll['total_net_amount'] =
            number_format($total_net_amount, 2, '.', '');
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

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE MONTHS
    |--------------------------------------------------------------------------
    */

    $months = [

        'january_amount' => 0,
        'february_amount' => 0,
        'march_amount' => 0,
        'april_amount' => 0,
        'may_amount' => 0,
        'june_amount' => 0,

        'july_amount' => 0,
        'august_amount' => 0,
        'september_amount' => 0,
        'october_amount' => 0,
        'november_amount' => 0,
        'december_amount' => 0,

    ];

    /*
    |--------------------------------------------------------------------------
    | COVERAGE
    |--------------------------------------------------------------------------
    */

    $start = Carbon::parse(
        $payroll->coverage_from
    );

    $end = Carbon::parse(
        $payroll->coverage_to
    );

    /*
    |--------------------------------------------------------------------------
    | LATEST PAYROLL ITEM
    |--------------------------------------------------------------------------
    */

    $latestPayrollItem =
    \DB::table('payroll_salary_items as psi')

        ->join(
            'payroll_salary as ps',
            'ps.id',
            '=',
            'psi.payroll_id'
        )

        ->where(
            'psi.employee_no',
            $emp->employee_no
        )

        ->where(
            'ps.employment_type',
            $emp->employment_type_id
        )

        ->where(
            'ps.status',
            'approved'
        )

        ->select(
            'psi.basic_salary',
            'psi.aut'
        )

        ->orderBy(
            'ps.payroll_date',
            'desc'
        )

        ->first();

        Log::info('Single ADD LATEST PAYROLL ITEM', [

            'employee_no' => $emp->employee_no,
        
            'latestPayrollItem' => $latestPayrollItem,
        
        ]);

    /*
    |--------------------------------------------------------------------------
    | DEFAULT VALUES
    |--------------------------------------------------------------------------
    */

    $defaultSalary = round(
        floatval(
            $latestPayrollItem->basic_salary
                ?? $basicSalary
        ),
        2
    );

    $defaultAut = round(
        floatval(
            $latestPayrollItem->aut
                ?? 0
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | PAYROLL ROWS
    |--------------------------------------------------------------------------
    */

    $salaryItems =
                    \DB::table('payroll_salary_items as psi')
    
                        ->join(
                            'payroll_salary as ps',
                            'ps.id',
                            '=',
                            'psi.payroll_id'
                        )
    
                        ->where(
                            'psi.employee_no',
                            $emp->employee_no
                        )
    
                        ->where(
                            'ps.employment_type',
                            $emp->employment_type_id
                        )
    
                        ->where(
                            'ps.status',
                            'approved'
                        )
    
                        ->whereBetween(
                            'ps.payroll_date',
                            [
                                $payroll->coverage_from,
                                $payroll->coverage_to
                            ]
                        )
    
                        ->select(
                            'ps.payroll_date',
                            'psi.basic_salary',
                            'psi.aut'
                        )
    
                        ->orderBy(
                            'ps.payroll_date',
                            'desc'
                        )
    
                        ->get()
    
                        ->groupBy(function ($item) {
    
                            return Carbon::parse(
                                $item->payroll_date
                            )->format('Y-m');
    
                        });

                        Log::info('Single Add RAW SALARY ITEMS', [

                            'employee_no' => $emp->employee_no,
                        
                            'salaryItems' => $salaryItems->toArray(),
                        
                        ]);
    
    /*
    |--------------------------------------------------------------------------
    | GENERATE MONTHS
    |--------------------------------------------------------------------------
    */

    $current = $start->copy();

    $generatedMonths = [];

    while ($current <= $end) {

        $monthKey =
                        $current->format('Y-m');
    
                    $monthPayrolls =
                        $salaryItems[$monthKey]
                        ?? collect();
    
                    $latestMonthPayroll =
                        $monthPayrolls->first();
    
                    $generatedMonths[] = [
    
                        'month_key' => $monthKey,
    
                        'month_name' => strtolower(
                            $current->format('F')
                        ),
    
                        'salary' => round(
                            floatval(

                                $monthPayrolls->isNotEmpty()

                                    ? $latestMonthPayroll->basic_salary

                                    : $basicSalary

                            ),
                            2
                        ),
    
                        'aut' => round(
                            floatval(

                                $monthPayrolls->isNotEmpty()

                                    ? $monthPayrolls->max('aut')

                                    : 0

                            ),
                            2
                        ),
    
                    ];

                    Log::info('GENERATED MONTH', [

                        'employee_no' => $emp->employee_no,
                    
                        'month_key' => $monthKey,
                    
                        'month_name' => strtolower(
                            $current->format('F')
                        ),
                    
                        'salary' => round(
                            floatval(
                                $latestMonthPayroll->basic_salary
                                    ?? $defaultSalary
                            ),
                            2
                        ),
                    
                        'aut' => round(
                            floatval(
                                $monthPayrolls->max('aut')
                                    ?? $defaultAut
                            ),
                            2
                        ),
                    
                    ]);
    
                    $current->addMonth();
    }

    /*
    |--------------------------------------------------------------------------
    | COMPUTE MONTHLY PREMIUM
    |--------------------------------------------------------------------------
    */

    $semesterTotal = 0;

    foreach ($generatedMonths as $salaryItem) {

        $month =
            $salaryItem['month_name'];

        $salary = round(
            floatval(
                $salaryItem['salary'] ?? 0
            ),
            2
        );

        $aut = round(
            floatval(
                $salaryItem['aut'] ?? 0
            ),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | PREMIUM FORMULA
        |--------------------------------------------------------------------------
        */

        $netBase = $salary - $aut;

        $premiumAmount = round(
            $netBase * 0.20,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | MONTH COLUMN
        |--------------------------------------------------------------------------
        */

        $column = $month . '_amount';

        if (array_key_exists($column, $months)) {

            $months[$column] =
                $premiumAmount;
        }

        $semesterTotal += $premiumAmount;
    }

    /*
    |--------------------------------------------------------------------------
    | PERCENTAGE
    |--------------------------------------------------------------------------
    */

    $percentage = round(
        floatval(
            $payroll->percentage ?? 100
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | BONUS
    |--------------------------------------------------------------------------
    */

    $bonus = round(
        $semesterTotal *
        ($percentage / 100),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | TAX
    |--------------------------------------------------------------------------
    */

    $tax = round(
        $semesterTotal * 0.05,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | NET
    |--------------------------------------------------------------------------
    */

    $net = round(
        $semesterTotal - $tax,
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

        /*
        |--------------------------------------------------------------------------
        | MONTHLY PREMIUMS
        |--------------------------------------------------------------------------
        */

        'january_amount' =>
            $months['january_amount'],

        'february_amount' =>
            $months['february_amount'],

        'march_amount' =>
            $months['march_amount'],

        'april_amount' =>
            $months['april_amount'],

        'may_amount' =>
            $months['may_amount'],

        'june_amount' =>
            $months['june_amount'],

        'july_amount' =>
            $months['july_amount'],

        'august_amount' =>
            $months['august_amount'],

        'september_amount' =>
            $months['september_amount'],

        'october_amount' =>
            $months['october_amount'],

        'november_amount' =>
            $months['november_amount'],

        'december_amount' =>
            $months['december_amount'],

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        'total_amount' =>
            round($semesterTotal, 2),

        'percentage' =>
            $percentage,

        'bonus' =>
            $bonus,

        'cash_gift' => 0,

        'tax' =>
            $tax,

        'net_amount' =>
            $net,

        /*
        |--------------------------------------------------------------------------
        | COVERAGE
        |--------------------------------------------------------------------------
        */

        'coverage_from' =>
            $payroll->coverage_from,

        'coverage_to' =>
            $payroll->coverage_to,

        'semester' =>
            $payroll->semester,

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

                'payroll_id' =>
                    $this->payroll_id,
            
                'employee_no' =>
                    $this->selectedEmployee['employee_no'],
            
                'employment_type_id' =>
                    $this->selectedEmployee['employment_type'],
            
                'name' =>
                    $this->selectedEmployee['name'],
            
                'position' =>
                    $positionName,
            
                'basic_salary' =>
                    $this->selectedEmployee['basic_salary'],
            
                /*
                |--------------------------------------------------------------------------
                | MONTHLY PREMIUMS
                |--------------------------------------------------------------------------
                */
            
                'january_amount' =>
                    $this->selectedEmployee['january_amount'] ?? 0,
            
                'february_amount' =>
                    $this->selectedEmployee['february_amount'] ?? 0,
            
                'march_amount' =>
                    $this->selectedEmployee['march_amount'] ?? 0,
            
                'april_amount' =>
                    $this->selectedEmployee['april_amount'] ?? 0,
            
                'may_amount' =>
                    $this->selectedEmployee['may_amount'] ?? 0,
            
                'june_amount' =>
                    $this->selectedEmployee['june_amount'] ?? 0,
            
                'july_amount' =>
                    $this->selectedEmployee['july_amount'] ?? 0,
            
                'august_amount' =>
                    $this->selectedEmployee['august_amount'] ?? 0,
            
                'september_amount' =>
                    $this->selectedEmployee['september_amount'] ?? 0,
            
                'october_amount' =>
                    $this->selectedEmployee['october_amount'] ?? 0,
            
                'november_amount' =>
                    $this->selectedEmployee['november_amount'] ?? 0,
            
                'december_amount' =>
                    $this->selectedEmployee['december_amount'] ?? 0,
            
                /*
                |--------------------------------------------------------------------------
                | TOTALS
                |--------------------------------------------------------------------------
                */
            
                'total_amount' =>
                    $this->selectedEmployee['total_amount'] ?? 0,
            
                'percentage' =>
                    $this->selectedEmployee['percentage'] ?? 100,
            
                'remarks' =>
                    $this->selectedEmployee['remarks'] ?? null,
            
                'bonus' =>
                    $this->selectedEmployee['bonus'] ?? 0,
            
                'cash_gift' =>
                    $this->selectedEmployee['cash_gift'] ?? 0,
            
                'date_hired' =>
                    $this->selectedEmployee['date_hired'] ?? null,
            
                'coverage_from' =>
                    $this->selectedEmployee['coverage_from'] ?? null,
            
                'coverage_to' =>
                    $this->selectedEmployee['coverage_to'] ?? null,
            
                'semester' =>
                    $this->selectedEmployee['semester'] ?? null,
            
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

                'january_amount',
                'february_amount',
                'march_amount',
                'april_amount',
                'may_amount',
                'june_amount',
            
                'july_amount',
                'august_amount',
                'september_amount',
                'october_amount',
                'november_amount',
                'december_amount',
            
                'total_amount',
            
                'percentage',
            
                'bonus',
            
                'tax',
            
                'net_amount'
            
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

        $totalBonus = 0;
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

                $payrollItem = BonusItemsPayroll::find(
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

                    'january_amount' => $employeeData['january_amount'] ?? 0,
                    'february_amount' => $employeeData['february_amount'] ?? 0,
                    'march_amount' => $employeeData['march_amount'] ?? 0,
                    'april_amount' => $employeeData['april_amount'] ?? 0,
                    'may_amount' => $employeeData['may_amount'] ?? 0,
                    'june_amount' => $employeeData['june_amount'] ?? 0,

                    'july_amount' => $employeeData['july_amount'] ?? 0,
                    'august_amount' => $employeeData['august_amount'] ?? 0,
                    'september_amount' => $employeeData['september_amount'] ?? 0,
                    'october_amount' => $employeeData['october_amount'] ?? 0,
                    'november_amount' => $employeeData['november_amount'] ?? 0,
                    'december_amount' => $employeeData['december_amount'] ?? 0,

                    'total_amount' => $employeeData['total_amount'] ?? 0,

                    'percentage' => $employeeData['percentage'] ?? 100,

                    'bonus' => $employeeData['bonus'] ?? 0,

                    'tax' => $employeeData['tax'] ?? 0,

                    'net_amount' => $employeeData['net_amount'] ?? 0,

                ];

                $payrollItem->update($updateData);

                /*
                |--------------------------------------------------------------------------
                | RECOMPUTE PAYROLL TOTALS
                |--------------------------------------------------------------------------
                */

                $totalBonus += floatval(
                    $employeeData['bonus'] ?? 0
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

        BonusPayroll::where('id', $this->payroll_id)
            ->update([

                'updated_at' => now()

            ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE LOCAL RECORDS
        |--------------------------------------------------------------------------
        */

        $this->records['payroll']['total_bonus']
            = number_format($totalBonus, 2, '.', '');

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
        return view('livewire.admin.payroll.process.premium');
    }
}
