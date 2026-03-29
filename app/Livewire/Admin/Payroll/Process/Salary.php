<?php

namespace App\Livewire\Admin\Payroll\Process;

use App\Http\Controllers\Admin\Services\Payroll\SalaryService;
use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use Illuminate\Support\Facades\DB;
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

    protected $listeners = ['save', 'approve'];

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
                    'emergency_loan','plreg','mpl','mpl_lite','cpl','mp2',
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

   

    // -------------------------------
    // Sync user-editable fields
    // -------------------------------
    $editableFields = [
        'basic_salary', 'pera',
        'hdmf','uca', 'disallowance', 'dbp','kawani','philhealth','consoloan',
        'emergency_loan','plreg','mpl','mpl_lite','cpl','mp2',
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

    // If pera exists, add it
   //dd($payrollItem['employment_type_id'] );
   if($payrollItem['employment_type_id'] !== 2 && $payrollItem['employment_type_id'] !== 3 && $payrollItem['employment_type_id'] !== 4) {
        $gross = round($basic + $pera, 2);
   }else{
        $gross = round($basic, 2);
   }

    // Override gross
    $payrollItem['gross_amount_earned'] = $gross;
    $this->gross_amount_earned[$sectionIndex][$employeeIndex] = $gross;

    $hasTax3 = array_key_exists('tax_3', $original)
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
        && floatval($original['tax_10']) != 0;

    //dd($hasTax1, $hasTax5 , $hasTax6, $hasTax11, $aut); 
    
    if($hasTax3){
        $t3 = $basic - $aut;  
        $ctax_3 = round($t3 * 0.03, 2);
        $this->tax_3[$sectionIndex][$employeeIndex] = $ctax_3;
        $totalDeductions = $ctax_3; 
        $this->total_deductions[$sectionIndex][$employeeIndex] = $totalDeductions;
    }

    if($hasTax5){
        $t5 = $basic - $aut;  
        $ctax_5 = round($t5 * 0.05, 2);
        $this->tax_5[$sectionIndex][$employeeIndex] = $ctax_5;
        $totalDeductions = $ctax_5; 
        $this->total_deductions[$sectionIndex][$employeeIndex] = $totalDeductions;
    }

    if($hasTax8){
        $t8 = $basic - $aut;  
        $ctax_8 = round($t8 * 0.08, 2);
        $this->tax_8[$sectionIndex][$employeeIndex] = $ctax_8;
        $totalDeductions = $ctax_8; 
        $this->total_deductions[$sectionIndex][$employeeIndex] = $totalDeductions;
    }

    if($hasTax10){
        $t10 = $basic - $aut;  
        $ctax_10 = round($t10 * 1.10, 2);
        $this->tax_10[$sectionIndex][$employeeIndex] = $ctax_10;
        $totalDeductions = $ctax_10; 
        $this->total_deductions[$sectionIndex][$employeeIndex] = $totalDeductions;
    }

    // -------------------------------
    // Compute total deductions
    // -------------------------------
    $deductionFields = [
        'rlip','hdmf','philhealth','consoloan','emergency_loan',
        'plreg','mpl','mpl_lite','cpl','mp2','mplstlms','cir375_cir449',
        'uca','disallowance', 'w_tax','overpayment','tax_3', 'tax_5', 'tax_8','tax_10','aut'
    ];

    $totalDeductions = 0;
    foreach ($deductionFields as $f) {
        $totalDeductions += floatval($payrollItem[$f] ?? 0);
    }
    $totalDeductions = round($totalDeductions, 2);

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

            $firstHalf = round((float) $firstHalf, 2);
            $secondHalf = round($lbpPayroll - $firstHalf, 2);
        }else{

            $firstHalf  = floor(($netAmount / 2) * 100) / 100;
                       // dd($firstHalf);
            $firstHalf =  $firstHalf - $bankTotal ;
            $firstHalf = round((float) $firstHalf, 2);
            $secondHalf = round($lbpPayroll - $firstHalf, 2);

        }

        
        // If dbp/kawani already exist in DB, keep the stored first half
        // and recompute only the second half.
        
    } elseif ($isFirstHalf) {
        // First cutoff (1–15): recompute first half ONLY
       
        $firstHalf  = floor(($netAmount / 2) * 100) / 100;
        // dd($firstHalf);
        //dd($firstHalf , $bankTotal);
        $firstHalf =  $firstHalf - $bankTotal ;
        $firstHalf = round((float) $firstHalf, 2);
        $secondHalf = round($lbpPayroll - $firstHalf, 2);
    } else {
        // Second cutoff (16–end): recompute second half ONLY
        $firstHalf  = round((float) ($original['net_first_half'] ?? 0), 2);
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
        $this->updatedItems[] = $payrollItem['id'];
        $this->updatedItems = array_unique($this->updatedItems);
    } else {
        $this->updatedItems = array_diff($this->updatedItems, [$payrollItem['id']]);
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




    /* ======================================================
     * SAVE
     * ====================================================== */
    public function save(bool $confirm = true)
    {
        if ($confirm) {
            $this->dispatch('showConfirmation', [
                'title' => 'Save changes?',
                'message' => 'This will update payroll computations.',
                'action' => 'save'
            ]);
            return;
        }

       

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
                        'mp2' => $row['mp2'],
                        'mplstlms' => $row['mplstlms'],
                        'cir375_cir449' => $row['cir375_cir449'],
                        'w_tax' => $row['w_tax'],
                        'aut' => $row['aut'],
                        'disallowance' => $row['disallowance'],
                        'kawani' => $row['kawani'],
                        'total_deductions' => $row['total_deductions'],
                        'net_amount' => $row['net_amount'],
                        'lbp_payroll_account' => $row['lbp_payroll_account'],
                        'net_first_half' => $row['net_first_half'],
                        'net_second_half' => $row['net_second_half'],
                        'salary' => $row['salary'],
                        'overpayment' => $row['overpayment'],
                        'tax_3' => $row['tax_3'],
                        'tax_5' => $row['tax_5'],
                        'tax_8' => $row['tax_8'],
                        'tax_10' => $row['tax_10'],
                    ]);
                }
            }
        });


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
