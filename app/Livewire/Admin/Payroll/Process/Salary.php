<?php

namespace App\Livewire\Admin\Payroll\Process;


use App\Http\Controllers\Admin\Services\Payroll\SalaryService;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use App\Models\EmployementTypes;
use App\Services\ContributionsService;
use App\Services\SummaryServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\DailyTimeRecordService;
use App\Models\Positions;
use App\Models\Tranche;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Salary extends Component
{
    public $product;
    public $type;
    public $employment_type;
    public $payroll_id;

    public $basic_salary = [];
    public $pera = [];
    public $gross_amount_earned = [];
    public $hdmf = [];
    public $uca = [];
    public $dbp = [];
    public $kawani = [];
    public $rlip = [];
    public $philhealth = [];
    public $consoloan = [];
    public $emergency_loan = [];
    public $plreg = [];
    public $mpl = [];
    public $mpl_lite= [];
    public $cpl = [];
    public $gsel = [];
    public $mp2 = [];
    public $mplstlms = [];
    public $cir375_cir449 = [];
    public $w_tax = [];
    public $overpayment = [];
    public $disallowance = [];
    public $aut = [];
    public $net_first_half = [];
    public $net_second_half = [];

    public $tax_3 = [];
    public $tax_5 = [];
    public $tax_8 = [];
    public $tax_10 = [];

    public $total_deductions = [];
    public $net_amount = [];
    public $lbp_payroll_account = [];

    public array $originalItems = [];
    public array $updatedItems = [];

    public bool $isApproved = false;
    public bool $hasChanges = false;

    public $records;

    public $manualEdits = [];

    public bool $isFirstCutoff = false;
    public bool $isSecondCutoff = false;

    public $confirmingDelete = false;
    public $deleteSectionIndex;
    public $deleteEmployeeIndex;

    public $showAddModal = false;
    public $searchEmployee = '';
    public $employeeResults = [];
    public $selectedEmployee = null;
    public $showDuploicateLabel = false;

    public array $newItems = [];

    protected $listeners = ['save', 'approve', 'deleteEmployee', 'confirmSave'];

    /* ======================================================
     * MOUNT
     * ====================================================== */
    public function mount()
    {
        $this->loadRecords();
    }

    /* ======================================================
     * DETERMINE PAYROLL HALF
     * ====================================================== */
    private function isFirstHalf(): bool
    {
        $cutoff = $this->records['payroll']['cut_off_period'] ?? '';

        // Example format: "01 to 15", "16 to 30"
        if (preg_match('/(\d+)\s*to\s*(\d+)/', $cutoff, $matches)) {
            $start = intval($matches[1]);
            $end = intval($matches[2]);
            return $start <= 15; // First half cutoff if start day <= 15
        }

        // Default fallback
        return false;
    }

    /* ======================================================
     * LOAD RECORDS
     * ====================================================== */
    public function loadRecords()
    {
        $this->product = config('app.product');

        $service = app(SalaryService::class);
        $records = $service->getPayroll($this->payroll_id);

        foreach ($records['payroll_items'] as $s => $section) {
            foreach ($section['employees'] as $e => $row) {
                foreach ([
                    'basic_salary', 'pera', 'gross_amount_earned',
                    'hdmf','uca', 'disallowance', 'dbp','kawani','rlip','philhealth','consoloan',
                    'emergency_loan','plreg','mpl','mpl_lite','cpl','mp2','gsel',
                    'mplstlms','cir375_cir449','w_tax', 'overpayment', 'tax_3', 'tax_5', 'tax_8', 'tax_10', 'aut',
                    'total_deductions','net_amount','lbp_payroll_account','net_first_half','net_second_half'
                ] as $f) {
                    $this->{$f}[$s][$e] = $row[$f] ?? 0;
                }
            }
        }

        $this->originalItems = json_decode(json_encode($records['payroll_items']), true);

        $this->isApproved = $records['payroll']['status'] === 'approved';
        $this->records = $records;

        $this->isFirstCutoff  = $this->isFirstHalf();
        $this->isSecondCutoff = ! $this->isFirstCutoff;
    }

    /* ======================================================
     * RECOMPUTE
     * ====================================================== */



public function recompute($sectionIndex, $employeeIndex, $field = null)
{
    if ($this->isApproved) return;

    $payrollItem = &$this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];
    $original    = $this->originalItems[$sectionIndex]['employees'][$employeeIndex] ?? [];

    $this->hasChanges = true;

    // -------------------------------
    // Sync user-editable fields
    // -------------------------------
    $editableFields = [
        'basic_salary', 'pera',
        'hdmf','uca', 'disallowance', 'dbp','kawani','philhealth','consoloan',
        'emergency_loan','plreg','mpl','mpl_lite','cpl','mp2','gsel',
        'mplstlms','cir375_cir449','w_tax','overpayment', 'tax_3', 'tax_5', 'tax_8', 'tax_10', 'aut','rlip'
    ];

    foreach ($editableFields as $f) {
        $payrollItem[$f] = round(floatval($this->{$f}[$sectionIndex][$employeeIndex] ?? 0), 2);
    }

    // -------------------------------
    // Compute GROSS dynamically
    // -------------------------------
    $basic = floatval($payrollItem['basic_salary'] ?? 0);
    $pera  = floatval($payrollItem['pera'] ?? 0);
    $aut = floatval($payrollItem['aut'] ?? 0);
    $rate = 0.05;
    $ceiling = 100000;

    // If pera exists, add it
   //dd($payrollItem['employment_type_id'] );
   if($payrollItem['employment_type_id'] !== 2 && $payrollItem['employment_type_id'] !== 3 && $payrollItem['employment_type_id'] !== 4) {
        $gross = round($basic + $pera, 2);

        $philhealth = floor((min($basic, $ceiling) * $rate / 2) * 100) / 100;
        $this->philhealth[$sectionIndex][$employeeIndex] =  $philhealth ;
        $payrollItem['philhealth'] = $philhealth ; // ✅ IMPORTANT

        $rlip = floor(floatval($basic * 0.09) * 100) / 100;
        $this->rlip[$sectionIndex][$employeeIndex] = $rlip;
        $payrollItem['rlip'] = $rlip ; // ✅ IMPORTANT

   }else{
        $gross = round($basic, 2);

        $philhealth = floor(($basic * 0.05) * 100) / 100;
        $this->philhealth[$sectionIndex][$employeeIndex] =  $philhealth ;
        $payrollItem['philhealth'] =  $philhealth ; // ✅ IMPORTANT
                       
   }

    // Override gross
    $payrollItem['gross_amount_earned'] = $gross;
    $this->gross_amount_earned[$sectionIndex][$employeeIndex] = $gross;

    /*$hasTax3 = array_key_exists('tax_3', $original)
        && $original['tax_3'] !== null
        && floatval($original['tax_3']) != 0;
    
    $hasTax5 = array_key_exists('tax_5', $original)
        && $original['tax_5'] !== null
        && floatval($original['tax_5']) != 0;

    $hasTax8 = array_key_exists('tax_8', $original)
        && $original['tax_8'] !== null
        && floatval($original['tax_8']) != 0;
        
    $hasTax10 = array_key_exists('tax_10', $original)
        && $original['tax_10'] !== null
        && floatval($original['tax_10']) != 0;*/
    
    $hasTax3 = isset($payrollItem['tax_3']) && $payrollItem['tax_3'] > 0;
    $hasTax5 = isset($payrollItem['tax_5']) && $payrollItem['tax_5'] > 0;
    $hasTax8 = isset($payrollItem['tax_8']) && $payrollItem['tax_8'] > 0;
    $hasTax10 = isset($payrollItem['tax_10']) && $payrollItem['tax_10'] > 0;

   // dd($hasTax3, $hasTax5 , $hasTax8, $hasTax10, $aut); 
   Log::info('hastax', ['tax3' => $hasTax3,'tax5' => $hasTax5,'tax8' => $hasTax8,'tax10' => $hasTax10, 'aut' => $aut]);
    
    if($hasTax3){
        $t3 = $basic - $aut;  
        $ctax_3 = round($t3 * 0.03, 2);
    
        $this->tax_3[$sectionIndex][$employeeIndex] = $ctax_3;
        $payrollItem['tax_3'] = $ctax_3; // ✅ IMPORTANT
    }
    
    if($hasTax5){
        $t5 = $basic - $aut;  
        $ctax_5 = round($t5 * 0.05, 2);
    
        $this->tax_5[$sectionIndex][$employeeIndex] = $ctax_5;
        $payrollItem['tax_5'] = $ctax_5; // ✅
    }
    
    if($hasTax8){
        $t8 = $basic - $aut;  
        $ctax_8 = round($t8 * 0.08, 2);
    
        $this->tax_8[$sectionIndex][$employeeIndex] = $ctax_8;
        $payrollItem['tax_8'] = $ctax_8; // ✅
    }
    
    if($hasTax10){
        $t10 = $basic - $aut;  
        $ctax_10 = round($t10 * 0.10, 2);
    
        $this->tax_10[$sectionIndex][$employeeIndex] = $ctax_10;
        $payrollItem['tax_10'] = $ctax_10; // ✅
    }

    // -------------------------------
    // Compute total deductions
    // -------------------------------
    $deductionFields = [
        'rlip','hdmf','philhealth','consoloan','emergency_loan',
        'plreg','mpl','mpl_lite','cpl','mp2','mplstlms','cir375_cir449','gsel',
        'uca','disallowance', 'w_tax','aut','overpayment','tax_3', 'tax_5', 'tax_8','tax_10'
    ];

    $totalDeductions = 0;
    foreach ($deductionFields as $f) {
        $totalDeductions += floatval($payrollItem[$f] ?? 0);
    }
    $totalDeductions = round($totalDeductions, 2);

    Log::info('total', ['totalDeductions' => $totalDeductions]);

    // -------------------------------
    // Compute net amount
    // -------------------------------
    $gross = floatval($payrollItem['gross_amount_earned'] ?? 0);
    $netAmount = round($gross - $totalDeductions, 2);

    // -------------------------------
    // Compute LBP payroll account
    // -------------------------------
    $bankTotal = round(($payrollItem['dbp'] ?? 0) + ($payrollItem['kawani'] ?? 0), 2);
    $lbpPayroll = round($netAmount - $bankTotal, 2);

   // dd($payrollItem['dbp']);

    $hasDbp = array_key_exists('dbp', $original)
        && $original['dbp'] !== null
        && floatval($original['dbp']) != 0;
    $hasKawani = array_key_exists('kawani', $original)
        && $original['kawani'] !== null
        && floatval($original['kawani']) != 0;
    $hasAny = $hasDbp || $hasKawani;

    // -------------------------------
    // Determine cutoff and recompute halves
    // -------------------------------
    $isFirstHalf = $this->isFirstHalf();

    if ($hasAny) {

      
        if($this->isSecondCutoff){
           
            $firstHalf = $original['net_first_half'] ?? null;
       
            if ($firstHalf === null) {
                $firstHalf = $this->getFirstHalfFromPreviousPayroll($payrollItem);
            
            }
            Log::info('hasAny isSecondCutoff', ['firstHalf' => $firstHalf,'lbpPayroll' => $lbpPayroll ]);

            $firstHalf = round((float) $firstHalf, 2);
            $secondHalf = round($lbpPayroll - $firstHalf, 2);
        }else{
           
            $firstHalf  = floor(($netAmount / 2) * 100) / 100;
            Log::info('hasAny isSecondCutoff', ['firstHalf' => $firstHalf,'bankTotal' => $bankTotal,'netAmount' => $netAmount ]);

                       // dd($firstHalf);
            $firstHalf =  $firstHalf - $bankTotal ;
            $firstHalf = round((float) $firstHalf, 2);
            $secondHalf = round($lbpPayroll - $firstHalf, 2);

        }

        
        // If dbp/kawani already exist in DB, keep the stored first half
        // and recompute only the second half.
        
    } elseif ($isFirstHalf) {
        // First cutoff (1–15): recompute first half ONLY
        Log::info('First cutoff (1–15)', ['netAmount' => $netAmount,'bankTotal' => $bankTotal]);
        $firstHalf  = floor(($netAmount / 2) * 100) / 100;
        // dd($firstHalf);
        //dd($firstHalf , $bankTotal);
        $firstHalf =  $firstHalf - $bankTotal ;
        $firstHalf = round((float) $firstHalf, 2);
        $secondHalf = round($lbpPayroll - $firstHalf, 2);
    } else {
        // Second cutoff (16–end): recompute second half ONLY
        Log::info('Second cutoff (16–end)', ['originalfirstHalf' => $payrollItem['net_first_half'],'lbpPayroll' => $lbpPayroll]);
        $firstHalf  = round((float) ($payrollItem['net_first_half'] ?? 0), 2);
        $secondHalf = round($lbpPayroll - $firstHalf, 2);
    }

    // HARD LOCK: prevent editing wrong half
    if ($hasAny) {
        // Keep first half fixed when dbp/kawani already exist
        unset($this->manualEdits[$sectionIndex][$employeeIndex]['net_second_half']);
        $this->manualEdits[$sectionIndex][$employeeIndex]['net_first_half'] = true;
    } elseif ($isFirstHalf) {
        // First cutoff → second half must NEVER change
        $this->manualEdits[$sectionIndex][$employeeIndex]['net_second_half'] = true;
    } else {
        // Second cutoff → first half must NEVER change
        $this->manualEdits[$sectionIndex][$employeeIndex]['net_first_half'] = true;
    }

    // -------------------------------
    // Assign computed values if NOT manually edited
    // -------------------------------
    $computedValues = [
        'total_deductions'    => $totalDeductions,
        'net_amount'          => $netAmount,
        'lbp_payroll_account' => $lbpPayroll,
        'net_first_half'      => $firstHalf,
        'net_second_half'     => $secondHalf,
    ];

    foreach ($computedValues as $f => $value) {
        if (!($this->manualEdits[$sectionIndex][$employeeIndex][$f] ?? false)) {
            $payrollItem[$f] = $value;
            $this->{$f}[$sectionIndex][$employeeIndex] = $value;
        }
    }

    // -------------------------------
    // Track changes for save
    // -------------------------------
    if ($this->isChanged($payrollItem, $original)) {
        Log::info('haschange1', ['payrollItem' => $payrollItem,'original' => $original]);
        $this->updatedItems[] = $payrollItem['id'];
        $this->updatedItems = array_unique($this->updatedItems);
    } else {
        Log::info('haschange2', ['payrollItem' => $payrollItem,'original' => $original]);
       // $this->updatedItems = array_diff($this->updatedItems, [$payrollItem['id']]);
        $this->updatedItems[] = $payrollItem['id'];
        $this->updatedItems = array_unique($this->updatedItems);
    }

    $this->hasChanges = !empty($this->updatedItems);

    // -------------------------------
    // Sync Livewire input fields
    // -------------------------------
    $this->total_deductions[$sectionIndex][$employeeIndex]    = $totalDeductions;
    $this->net_amount[$sectionIndex][$employeeIndex]          = $netAmount;
    $this->lbp_payroll_account[$sectionIndex][$employeeIndex] = $lbpPayroll;
    $this->net_first_half[$sectionIndex][$employeeIndex]      = $firstHalf;
    $this->net_second_half[$sectionIndex][$employeeIndex]     = $secondHalf;
}


public function manualEdit($sectionIndex, $employeeIndex, $field)
{
    $this->manualEdits[$sectionIndex][$employeeIndex][$field] = true;
    $this->hasChanges = true;
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
        'basic_salary','pera','gross_amount_earned','hdmf','uca','dbp','kawani','rlip','philhealth',
        'consoloan','emergency_loan','plreg','mpl','mpl_lite','cpl','mp2','mplstlms','gsel',
        'cir375_cir449','w_tax','overpayment','tax_3','tax_5','tax_8','tax_10','aut',
        'total_deductions','net_amount','lbp_payroll_account','net_first_half','net_second_half'
    ];

    $employee = $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

    DB::transaction(function () use ($employee, $sectionIndex, $employeeIndex, $fields) {

        SalaryItemsPayroll::where('id', $employee['id'])->delete();

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
    $hasDeductions = true;

    $payroll = SalaryPayroll::find($this->payroll_id);

    $cutOffPeriod = $payroll?->cut_off_period;
   // $taxType = '';
   
    $emp = DB::table('employee_information as ei')
        ->leftJoin('employee_personal as ep', 'ei.employee_no', '=', 'ep.employee_no')
        ->where('ei.id', $id)
        ->select(
            'ei.employee_no',
            'ei.salary',
            'ei.position_id',
            'ei.section_id',
            'ei.w_tax',
            'ei.tax_type',
            'ei.step_id',
            'ei.salary_type',
            'ei.employment_type_id',
            'ep.bp_no',
            DB::raw("CONCAT(ep.firstname, ' ', ep.lastname) as name")
        )
        ->first();

        $this->showDuploicateLabel = false; 

        $other_service = new OtherServices;
        $dtr_service = new DailyTimeRecordService;
        $payroll_service = app(PayrollService::class);
        
        $employee_no = $emp->employee_no;
        $name = $emp->name;
        $position_id = $emp->position_id;
        $employment_type_id = $emp->employment_type_id;
        $eligible = $emp->employment_type_id;
        //$basic_salary = round(floatval($employee['salary']), 2);
        $salary_type = $emp->salary_type;
        // $gw_tax = $employee['w_tax'];
        $rate = 0.05;
        $ceiling = 100000;
        $stepId = $emp->step_id;

        if ($employment_type_id != 3 && $employment_type_id != 4) {


            $salaryGrade = Positions::where('id', $position_id)->value('salary_grade');

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

        $basic_salary = round(floatval($salary), 2);
        $salary_type = $salary_type;
        $gw_tax = $wtax;

        [$startDate, $endDate] = explode(' to ', $payroll->cut_off_period);
                $cutoffEndDate = Carbon::parse(trim($endDate))->toDateString(); 

                $monthYear = Carbon::parse($payroll->payroll_date)->format('m-Y');
                $cut_off_period = $other_service->splitDateRange($payroll->cut_off_period);

                $dtr = $dtr_service->getDailyTimeRecord($employee_no, $cut_off_period, true);

                $dtr_summary  = $dtr['summary'];

                $overtimeData = $payroll_service->computeOvertimePay($basic_salary, $dtr_summary['worked_days'], $dtr_summary['overtime_minues']);
                
                $overtime = $overtimeData['gross_ot_pay'];

                $earnings = $other_service->earnings($employee_no);
                $deductions = $hasDeductions ? $other_service->deductions($employee_no, $cutoffEndDate) : [];

                //$current_date = Carbon::parse($payroll->payroll_date)->format('m/Y');
                $current_date = Carbon::parse($payroll->payroll_date)->format('Y-m-d');

                

                $social_security = $hasDeductions
                    ? DB::table('social_security as gb')
                        ->join('social_security_items as gi', 'gb.id', '=', 'gi.social_security_id')
                        ->where('gb.billing_month', $current_date)
                        ->where('gi.bp_no', $emp->bp_no)
                        ->select('gi.consoloan', 'gi.emrgy_loan', 'gi.plreg', 'gi.mpl', 'gi.mpl_lite', 'gi.cpl')
                        ->first() ?? (object) []
                    : (object) [];

        // Earnings
        $pera = round(floatval(collect($earnings)->firstWhere('code', 'PERA')['amount'] ?? 0), 2);
        $gross = round($basic_salary + $pera, 2);

        /*$philhealth = $hasDeductions
                    ? round(min($basic_salary, $ceiling) * $rate / 2, 2)
                    : 0;*/
               // $philhealth = $hasDeductions ? round(floatval($basic_salary * 0.05), 2) : 0;

                $hdmf = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'HDMF')['amount'] ?? 0), 2) : 0;
                $mp2 = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'MP2')['amount'] ?? 0), 2) : 0;
                
                $cir = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'CIR')['amount'] ?? 0), 2) : 0;
                $mplCos = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'MPL')['amount'] ?? 0), 2) : 0;
                $auts = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'AUTS')['amount'] ?? 0), 2) : 0;
               // $w_tax = $hasDeductions ? round(floatval($payroll_service->computeWithholdingTax($basic_salary) ?? 0), 2) : 0;
               
               
                $uca = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'UCA')['amount'] ?? 0), 2) : 0;
                $consoloan = $hasDeductions ? round(floatval($social_security->consoloan ?? 0), 2) : 0;
                $emergency_loan = $hasDeductions ? round(floatval($social_security->emrgy_loan ?? 0), 2) : 0;
                $plreg = $hasDeductions ? round(floatval($social_security->plreg ?? 0), 2) : 0;
                $mplss = $hasDeductions ? round(floatval($social_security->mpl ?? 0), 2) : 0;
                $mpl_lite = $hasDeductions ? round(floatval($social_security->mpl_lite ?? 0), 2) : 0;
                $cpl = $hasDeductions ? round(floatval($social_security->cpl ?? 0), 2) : 0;

                $mpl = !empty($mplCos) ? $mplCos : $mplss;

                if ($employment_type_id != 1){
                   // $aut = $hasDeductions ? round(floatval($payroll_service->computeAutDeduction($dtr_summary, $basic_salary, $salary_type))) : 0;
                   $aut = $auts;
                   $mplstlms = 0;
                }else{
                    $aut = 0;
                    $mplstlms = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'MPLSTLMS')['amount'] ?? 0), 2) : 0;
                }
                // Optional deductions
               // $dbp = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'DBP')['amount'] ?? 0), 2) : 0;
               // $kawani = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'Kawani')['amount'] ?? 0), 2) : 0;

                      // ✅ COS TAX COMPUTATION
                      $tax_3 = 0;
                      $tax_5 = 0;
                      $tax_8 = 0;
                      $tax_10 = 0;
      
      
      
                    //  dd($hasDeductions, $social_security->consoloan );
                      if($eligible !== 2 && $eligible !== 3 && $eligible !== 4) {
                          // DEDUCTION FOR GOVERNMENT EMPLOYEES
                          $philhealth = $hasDeductions
                            ? floor((min($basic_salary, $ceiling) * $rate / 2) * 100) / 100
                            : 0;
                          $gsel = $hasDeductions ? round(floatval(collect($deductions)->firstWhere('code', 'GSEL')['amount'] ?? 0), 2) : 0;
                          //$rlip = $hasDeductions ? round(floatval($basic_salary * 0.09), 2) : 0;
                          $rlip = $hasDeductions ? floor(floatval($basic_salary * 0.09) * 100) / 100 : 0;
                          $w_tax = $hasDeductions ? round(floatval($gw_tax ?? 0), 2) : 0;
      
                      }else{
                        $salaryBase = max($basic_salary, 10000);
                        Log::info('Salary Philhealth items', ['salaryBase' => $salaryBase, 'basicSalary' => $basic_salary]);
                        $philhealth = $hasDeductions
                        ? floor(($salaryBase * 0.05) * 100) / 100
                        : 0;
                        
                          $gsel = 0;
                          $taxType = $emp->tax_type ?? null;
      
                         //dd($taxType);
      
                            $tax_3 = $tax_5 = $tax_8 = $tax_10 = 0;

                            $taxBase = max(0, $basic_salary - $aut);

                            switch ($taxType) {

                                case 'TAX_3': // 3%
                                    $tax_3 = round($taxBase * 0.03, 2);
                                    break;

                                case 'TAX_5': // 5%
                                    $tax_5 = round($taxBase * 0.05, 2);
                                    break;

                                case 'TAX_8': // 8%
                                    $tax_8 = round($taxBase * 0.08, 2);
                                    break;

                                case 'TAX_10': // 10%
                                    $tax_10 = round($taxBase * 0.10, 2);
                                    break;
                            }
      
                          $rlip =  0;
                          $w_tax = 0;
                      }
                
               $dbp = 0;
               $kawani = 0;

                if ($hasDeductions) {
                    $filteredDeductions = collect($deductions)->filter(function ($item) use ($cutoffEndDate) {
                        return empty($item['valid_until']) || $item['valid_until'] >= $cutoffEndDate;
                    });

                    $dbp = round(floatval($filteredDeductions->firstWhere('code', 'DBP')['amount'] ?? 0), 2);
                    $kawani = round(floatval($filteredDeductions->firstWhere('code', 'Kawani')['amount'] ?? 0), 2);
                }
                
                $total_deduction = $rlip + $hdmf + $philhealth + $consoloan + $emergency_loan +
                    $plreg + $mpl + $mpl_lite + $cpl + $mp2 + $mplstlms + $cir + $w_tax + 
                    $tax_3 + $tax_5 + $tax_8 + $tax_10 +  $uca + $aut + $gsel;

                Log::info('selected employee total deductions', ['total_deductions' =>  $total_deduction]);    

                $total_lbp =  $dbp +  $kawani;  

                if($eligible !== 2 && $eligible !== 3 && $eligible !== 4) {
                    $net = round($gross - $total_deduction, 2);
                }else{
                    $net = round($basic_salary - $total_deduction, 2);
                }

                if(!empty($total_lbp)){
                    $lbp = $net - $total_lbp;
                }else{
                    $lbp = $net;
                }
                $half = round($net / 2, 2);
               
                $isFirstHalf = $this->isFirstHalf();
                  //  dd($lbp);
                    // FIRST HALF PAYROLL (01–15)
                   if($isFirstHalf ){

                        if(!empty($total_lbp)){
                            // dd($net);
                            $firstHalf  = floor(($net  / 2) * 100) / 100;
                            // dd($firstHalf);
                            $firstHalf =  $firstHalf - $total_lbp;
                            $secondHalf = round($lbp - $firstHalf, 2);
                            
                        }else{
                            /*$firstHalf  = floor(($net  / 2) * 100) / 100;
                            $secondHalf = round($net - $firstHalf, 2);*/

                            $netCents = (int) round($net * 100);

                            $firstHalfCents = intdiv($netCents, 2);
                            $secondHalfCents = $netCents - $firstHalfCents;
    
                            $firstHalf = $firstHalfCents / 100;
                            $secondHalf = $secondHalfCents / 100;
                            Log::info('Firsthalf Computation Salary', ['Net' => $net, 'NetCents' => $netCents, 'firstHalfCents' => $firstHalfCents, 'secondHalfCents ' => $secondHalfCents, 'fisthalf' => $firstHalf, 'secondhalf' => $secondHalf]);
                        
                        }
                    }else{

                        if(!empty($total_lbp)){
                            // dd($net);
                            $firstHalf  = floor(($net  / 2) * 100) / 100;
                            // dd($firstHalf);
                            $firstHalf =  $firstHalf;
                           // $secondHalf =  round($firstHalf - $total_lbp, 2);
                            $secondHalf = round($lbp - $firstHalf, 2);
                            
                        }else{
                            /*$firstHalf  = floor(($net  / 2) * 100) / 100;
                            $secondHalf = round($net - $firstHalf, 2);*/

                            $netCents = (int) round($net * 100);

                            $firstHalfCents = intdiv($netCents, 2);
                            $secondHalfCents = $netCents - $firstHalfCents;
    
                            $firstHalf = $firstHalfCents / 100;
                            $secondHalf = $secondHalfCents / 100;
                            Log::info('Secondhalf Computation Salary Service', ['Net' => $net, 'NetCents' => $netCents, 'firstHalfCents' => $firstHalfCents, 'secondHalfCents ' => $secondHalfCents, 'fisthalf' => $firstHalf, 'secondhalf' => $secondHalf]);
                        
                        }

                    }   

        $firstHalfRecord = $this->getFirstHalfPayrollItem($employee_no, $payroll);

        if ($firstHalfRecord) {

            $hasTax3 = isset($tax_3) && $tax_3 > 0;
            $hasTax5 = isset($tax_5) && $tax_5 > 0;
            $hasTax8 = isset($tax_8) && $tax_8 > 0;
            $hasTax10 = isset($tax_10) && $tax_10 > 0;

            $ctax_3 = 0;
            $ctax_5 = 0;
            $ctax_8 = 0;
            $ctax_10 = 0;

            if($hasTax3){
                $t3 = $firstHalfRecord->basic_salary - $firstHalfRecord->aut;  
                $ctax_3 = round($t3 * 0.03, 2);
            
            }
            
            if($hasTax5){
                $t5 = $firstHalfRecord->basic_salary - $firstHalfRecord->aut;   
                $ctax_5 = round($t5 * 0.05, 2);
            }
            
            if($hasTax8){
                $t8 = $firstHalfRecord->basic_salary - $firstHalfRecord->aut;   
                $ctax_8 = round($t8 * 0.08, 2);
            }
            
            if($hasTax10){
                $t10 = $firstHalfRecord->basic_salary - $firstHalfRecord->aut;   
                $ctax_10 = round($t10 * 0.10, 2);
            }

            $fh_total_deduction = $firstHalfRecord->rlip + $firstHalfRecord->hdmf + $firstHalfRecord->philhealth + $firstHalfRecord->consoloan + $firstHalfRecord->emergency_loan +
            $firstHalfRecord->plreg + $firstHalfRecord->mpl + $firstHalfRecord->mpl_lite + $firstHalfRecord->cpl + $firstHalfRecord->mp2 + $firstHalfRecord->mplstlms + $firstHalfRecord->cir375_cir449 + $firstHalfRecord->w_tax + 
            $ctax_3 + $ctax_5 + $ctax_8 + $ctax_10 +  $firstHalfRecord->uca + $firstHalfRecord->aut + $firstHalfRecord->disallowance + $firstHalfRecord->overpayment + $firstHalfRecord->gsel;

            Log::info('selected employee firstHalfRecord', ['data' =>  $firstHalfRecord, 'tax3' => $ctax_3, 'tax5' => $ctax_5, 'tax8' => $ctax_8, 'tax10' => $ctax_10]);

            if($firstHalfRecord->employment_type_id !== 2 && $firstHalfRecord->employment_type_id !== 3 && $firstHalfRecord->employment_type_id !== 4) {
                $fh_net = round($firstHalfRecord->gross_amount_earned - $fh_total_deduction, 2);
            }else{
                $fh_net = round($firstHalfRecord->basic_salary - $fh_total_deduction, 2);
            }

            $fh_total_lbp =  $firstHalfRecord->dbp +  $firstHalfRecord->kawani;

            if(!empty($fh_total_lbp)){
                $fh_lbp = $fh_net - $fh_total_lbp;
            }else{
                $fh_lbp = $fh_net;
            }

           /* if(!empty($fh_total_lbp)){
                // dd($net);
                $firstHalf  = floor(($net  / 2) * 100) / 100;
                // dd($firstHalf);
                $firstHalf = $firstHalfRecord->net_first_half;
                $secondHalf =  round($firstHalfRecord->net_first_half - $fh_total_lbp, 2);
               // $secondHalf = round($lbp - $firstHalf, 2);
                
            }else{
                $firstHalf  = floor(($net  / 2) * 100) / 100;
                $secondHalf = round($fh_net  - $firstHalfRecord->net_first_half, 2);
            }*/

            $secondHalf =  round($fh_lbp - $firstHalfRecord->net_first_half, 2);
           
            
            
            $this->selectedEmployee = [
                'employee_no' => $firstHalfRecord->employee_no,
                'name' => $firstHalfRecord->name,
                'position_id' => $emp->position_id,
                'position' => $this->getPositionName($emp->position_id), // ✅ ADD THIS
                'section_id' => $emp->section_id,
                'employment_type_id' => $firstHalfRecord->employment_type_id,
                'basic_salary' => $firstHalfRecord->basic_salary ?? 0,
                'pera' => $firstHalfRecord->pera,
                'gross_amount_earned' => $firstHalfRecord->gross_amount_earned,
                'rlip' => $firstHalfRecord->rlip,
                'hdmf' => $firstHalfRecord->hdmf,
                'philhealth' => $firstHalfRecord->philhealth,
                'consoloan' => $firstHalfRecord->consoloan,
                'emergency_loan' => $firstHalfRecord->emergency_loan,
                'plreg' => $firstHalfRecord->plreg,
                'mpl' => $firstHalfRecord->mpl,
                'mpl_lite' => $firstHalfRecord->mpl_lite,
                'cpl' => $firstHalfRecord->cpl,
                'gsel' => $firstHalfRecord->gsel,
                'mp2' => $firstHalfRecord->mp2,
                'mplstlms' => $firstHalfRecord->mplstlms,
                'cir375_cir449' => $firstHalfRecord->cir375_cir449,
                'w_tax' => $firstHalfRecord->w_tax,
                'uca' => $firstHalfRecord->uca,
                'aut' => $firstHalfRecord->aut,
                'disallowance' => $firstHalfRecord->disallowance,
                'overpayment' => $firstHalfRecord->overpayment,
                'total_deductions' => round($fh_total_deduction, 2),
                'net_amount' => round($fh_net, 2),
                'dbp' => $firstHalfRecord->dbp,
                'kawani' => $firstHalfRecord->kawani,
                'lbp_payroll_account' => round($fh_lbp, 2),
                'salary' => $firstHalfRecord->salary,
                'net_first_half' => $firstHalfRecord->net_first_half,
                'net_second_half' => $secondHalf,
                'is_first_half_locked' => 1,
                'is_second_half_locked' => 1,
                'tax_3' => $ctax_3,
                'tax_5' => $ctax_5,
                'tax_8' => $ctax_8,
                'tax_10' => $ctax_10,
            ];

        }else{

            $this->selectedEmployee = [
                'employee_no' => $emp->employee_no,
                'name' => $emp->name,
                'position_id' => $emp->position_id,
                'position' => $this->getPositionName($emp->position_id), // ✅ ADD THIS
                'section_id' => $emp->section_id,
                'employment_type_id' => $emp->employment_type_id,
                'basic_salary' => $basic_salary ?? 0,
                'pera' => $pera,
                'gross_amount_earned' => $gross,
                'overtime_pay' => $overtime,
                'rlip' => $rlip,
                'hdmf' => $hdmf,
                'philhealth' => $philhealth,
                'consoloan' => $consoloan,
                'emergency_loan' => $emergency_loan,
                'plreg' => $plreg,
                'mpl' => $mpl,
                'mpl_lite' => $mpl_lite,
                'cpl' => $cpl,
                'gsel' => $gsel,
                'mp2' => $mp2,
                'mplstlms' => $mplstlms,
                'cir375_cir449' => $cir,
                'w_tax' => $w_tax,
                'uca' => $uca,
                'aut' => $aut,
                'disallowance' => 0,
                'overpayment' => 0,
                'total_deductions' => round($total_deduction, 2),
                'net_amount' => $net,
                'dbp' => $dbp,
                'kawani' => $kawani,
                'lbp_payroll_account' => $lbp,
                'salary' => $half,
                'net_first_half' => round($firstHalf, 2),
                'net_second_half' => round($secondHalf, 2),
                'is_first_half_locked' => 1,
                'is_second_half_locked' => 1,
                'tax_3' => $tax_3,
                'tax_5' => $tax_5,
                'tax_8' => $tax_8,
                'tax_10' => $tax_10,
            ];

        } 

    Log::info('selected employee to save in payroll items', ['data' =>  $this->selectedEmployee]);
}

private function getFirstHalfPayrollItem($employee_no, $payroll)
    {
        return SalaryItemsPayroll::query()
            ->where('employee_no', $employee_no)
            ->whereHas('payroll', function ($q) use ($payroll) {
    
                $q->whereYear('payroll_date', \Carbon\Carbon::parse($payroll->payroll_date)->year)
                  ->whereMonth('payroll_date', \Carbon\Carbon::parse($payroll->payroll_date)->month)
    
                  // FIRST HALF ONLY
                  ->where('cut_off_period', 'like', '%01%15%')
    
                  // optional but recommended
                  ->where('status', 'approved');
            })
            ->latest('id') // get latest record
            ->first();
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

public function confirmAddEmployee()
{
    if (!$this->selectedEmployee) return;

    $exists = SalaryItemsPayroll::where('payroll_id', $this->payroll_id)
        ->where('employee_no', $this->selectedEmployee['employee_no'])
        ->exists();

    if ($exists) {

        //dd('duplicate');
        $this->showDuploicateLabel = true;

        $this->dispatch('alert', [
            'status' => 'error',
            'title' => 'Duplicate',
            'message' => 'Employee already exists in payroll'
        ]);
        return;
    }
    $this->hasChanges = true;
    DB::transaction(function () {

        $positionName = $this->getPositionName($this->selectedEmployee['position_id']);
        $sectionName  = $this->getSectionName($this->selectedEmployee['section_id']);

        $new = SalaryItemsPayroll::create([
            'payroll_id' => $this->payroll_id,
            'employee_no' => $this->selectedEmployee['employee_no'],
            'employment_type_id' => $this->selectedEmployee['employment_type_id'],
            'name' => $this->selectedEmployee['name'],
            'position' => $positionName,
            'basic_salary' => $this->selectedEmployee['basic_salary'],
            'pera' => $this->selectedEmployee['pera'],
            'gross_amount_earned' => $this->selectedEmployee['gross_amount_earned'],
            'hdmf' => $this->selectedEmployee['hdmf'],
            'uca' => $this->selectedEmployee['uca'],
            'dbp' => $this->selectedEmployee['dbp'],
            'rlip'  => $this->selectedEmployee['rlip'],
            'philhealth' => $this->selectedEmployee['philhealth'],
            'consoloan' => $this->selectedEmployee['consoloan'],
            'emergency_loan' => $this->selectedEmployee['emergency_loan'],
            'plreg' => $this->selectedEmployee['plreg'],
            'mpl' => $this->selectedEmployee['mpl'],
            'mpl_lite' => $this->selectedEmployee['mpl_lite'],
            'cpl' => $this->selectedEmployee['cpl'],
            'gsel' => $this->selectedEmployee['gsel'],
            'mp2' => $this->selectedEmployee['mp2'],
            'mplstlms' => $this->selectedEmployee['mplstlms'],
            'cir375_cir449' => $this->selectedEmployee['cir375_cir449'],
            'w_tax' => $this->selectedEmployee['w_tax'],
            'aut' => $this->selectedEmployee['aut'],
            'disallowance' => $this->selectedEmployee['disallowance'],
            'kawani' => $this->selectedEmployee['kawani'],
            'total_deductions' => $this->selectedEmployee['total_deductions'],
            'net_amount' => $this->selectedEmployee['net_amount'],
            'lbp_payroll_account' => $this->selectedEmployee['lbp_payroll_account'],
            'net_first_half' => $this->selectedEmployee['net_first_half'],
            'net_second_half' => $this->selectedEmployee['net_second_half'],
            'salary' => $this->selectedEmployee['basic_salary'],
            'overpayment' => $this->selectedEmployee['overpayment'],
            'tax_3' => $this->selectedEmployee['tax_3'],
            'tax_5' => $this->selectedEmployee['tax_5'],
            'tax_8' => $this->selectedEmployee['tax_8'],
            'tax_10' => $this->selectedEmployee['tax_10'],
        ]);

        $newItem = $new->toArray();

       
        Log::info('Add employee to salary', ['records' => $newItem]);

        $sectionIndex = $this->findOrCreateSection([
            'section_name' => $sectionName
        ]);

        $this->records['payroll_items'][$sectionIndex]['employees'][] = $newItem;

        $employeeIndex = count($this->records['payroll_items'][$sectionIndex]['employees']) - 1;

        // init fields
        foreach ([
            'basic_salary','pera','gross_amount_earned','hdmf','uca','dbp','kawani','rlip','philhealth',
            'consoloan','emergency_loan','plreg','mpl','mpl_lite','cpl','mp2','mplstlms','gsel',
            'cir375_cir449','w_tax','disallowance', 'overpayment','tax_3','tax_5','tax_8','tax_10','aut',
            'total_deductions','net_amount','lbp_payroll_account','net_first_half','net_second_half'
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
        'message' => 'Employee added to payroll'
    ]);
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




    /* ======================================================
     * SAVE
     * ====================================================== */
    public function save(bool $confirm = true)
    {
        if ($confirm) {
            $this->dispatch('showConfirmation', [
                'title' => 'Save changes?',
                'message' => 'This will update payroll computations.',
                'action' => 'confirmSave'
            ]);
            return;
        }
    }


     public function confirmSave()  
{
        DB::transaction(function () {
            foreach ($this->records['payroll_items'] as $sectionIndex => $section) {
                foreach ($section['employees'] as $employeeIndex => $row) {

                    // sync manually editable fields first
                    $row['total_deductions'] = $this->total_deductions[$sectionIndex][$employeeIndex] ?? $row['total_deductions'];
                    $row['net_amount'] = $this->net_amount[$sectionIndex][$employeeIndex] ?? $row['net_amount'];
                    $row['lbp_payroll_account'] = $this->lbp_payroll_account[$sectionIndex][$employeeIndex] ?? $row['lbp_payroll_account'];
                    $row['net_first_half'] = $this->net_first_half[$sectionIndex][$employeeIndex] ?? $row['net_first_half'];
                    $row['net_second_half'] = $this->net_second_half[$sectionIndex][$employeeIndex] ?? $row['net_second_half'];

                    Log::Debug('Updating Payroll Item ID: ' . $row['id'], $row);

                    SalaryItemsPayroll::where('id', $row['id'])->update([
                        'basic_salary' => $row['basic_salary'],
                        'pera' => $row['pera'],
                        'gross_amount_earned' => $row['gross_amount_earned'],
                        'hdmf' => $row['hdmf'],
                        'uca' => $row['uca'],
                        'dbp' => $row['dbp'],
                        'rlip'  => $row['rlip'],
                        'philhealth' => $row['philhealth'],
                        'consoloan' => $row['consoloan'],
                        'emergency_loan' => $row['emergency_loan'],
                        'plreg' => $row['plreg'],
                        'mpl' => $row['mpl'],
                        'mpl_lite' => $row['mpl_lite'],
                        'cpl' => $row['cpl'],
                        'gsel' => $row['gsel'],
                        'mp2' => $row['mp2'],
                        'mplstlms' => $row['mplstlms'],
                        'cir375_cir449' => $row['cir375_cir449'],
                        'w_tax' => $row['w_tax'],
                        'aut' => $row['aut'],
                        'disallowance' => $row['disallowance'] ?? 0,
                        'kawani' => $row['kawani'],
                        'total_deductions' => $row['total_deductions'],
                        'net_amount' => $row['net_amount'],
                        'lbp_payroll_account' => $row['lbp_payroll_account'],
                        'net_first_half' => $row['net_first_half'],
                        'net_second_half' => $row['net_second_half'],
                        'salary' => $row['salary'] ?? $row['basic_salary'] ?? 0,
                        'overpayment' => $row['overpayment'] ?? 0,
                        'tax_3' => $row['tax_3'],
                        'tax_5' => $row['tax_5'],
                        'tax_8' => $row['tax_8'],
                        'tax_10' => $row['tax_10'],
                    ]);
                }
            }
        });

        $this->newItems = [];
        $this->updatedItems = [];
        $this->hasChanges = false;

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Saved',
            'message' => 'Payroll updated successfully'
        ]);

        return redirect()->route('payroll.process', [
            'type' => $this->type,
            'payroll_id' => $this->payroll_id
        ]);
    }

    /* ======================================================
     * APPROVE
     * ====================================================== */
    public function approve(bool $confirm = true)
    {
        if ($confirm) {
            $this->dispatch('showConfirmation', [
                'title' => 'Approve payroll?',
                'message' => 'This action cannot be undone.',
                'action' => 'approve'
            ]);
            return;
        }

        DB::transaction(function () {
            SalaryPayroll::where('id', $this->payroll_id)
                ->update(['status' => 'approved']);

            SalaryItemsPayroll::where('payroll_id', $this->payroll_id)
                ->update(
                    $this->isFirstHalf()
                        ? ['is_first_half_locked' => 1]
                        : ['is_second_half_locked' => 1]
                );
        });

        return redirect()->route('payroll.process', [
            'type' => $this->type,
            'payroll_id' => $this->payroll_id
        ]);
    }

    /* ======================================================
     * CHANGE DETECTOR
     * ====================================================== */
    protected function isChanged(array $current, array $original): bool
    {
        foreach ($current as $k => $v) {
            if (isset($original[$k]) &&
                number_format((float)$v, 2) !== number_format((float)$original[$k], 2)
            ) {
                return true;
            }
        }
        return false;
    }

    private function getFirstHalfFromPreviousPayroll(array $payrollItem): float
    {
        $payroll = $this->records['payroll'];

        return SalaryItemsPayroll::query()
            ->where('employee_id', $payrollItem['employee_id'])
            ->whereHas('payroll', function ($q) use ($payroll) {
                $q->where('payroll_month', $payroll['payroll_month']) // Jan 2026
                ->where('cut_off_period', 'like', '%01%15%')
                ->where('status', 'approved');
            })
            ->value('net_first_half') ?? 0;
    }

    public function getIsLockedProperty(): bool
    {
        // Payroll is fully locked if approved OR both halves are locked for all employees
        return $this->isApproved;
    }

    public function isEmployeeLocked($sectionIndex, $employeeIndex): bool
    {
        $employee = $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];
        return $this->isApproved 
            || ($employee['is_first_half_locked'] ?? false) 
            && ($employee['is_second_half_locked'] ?? false);
    }



    public function render()
    {
        return view('livewire.admin.payroll.process.salary');
    }
}
