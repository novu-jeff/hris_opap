<?php

namespace App\Livewire\Admin\Payroll\Process;


use App\Http\Controllers\Admin\Services\Payroll\EmeService;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\PayrollEme;
use App\Models\PayrollEmeItems;
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

class Eme extends Component
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
    public $eme = [];
    public $net_amount = [];

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
     * LOAD RECORDS
     * ====================================================== */
    public function loadRecords()
    {
        $this->product = config('app.product');

        $service = app(EmeService::class);
        $records = $service->getPayroll($this->payroll_id);

        foreach ($records['payroll_items'] as $s => $section) {
            foreach ($section['employees'] as $e => $row) {
                foreach ([
                    'eme','net_amount'
                ] as $f) {
                    $this->{$f}[$s][$e] = $row[$f] ?? 0;
                }
            }
        }

        $this->originalItems = json_decode(json_encode($records['payroll_items']), true);

        $this->isApproved = $records['payroll']['status'] === 'approved';
        $this->records = $records;

       // $this->isFirstCutoff  = $this->isFirstHalf();
       // $this->isSecondCutoff = ! $this->isFirstCutoff;
    }

    /* ======================================================
     * RECOMPUTE
     * ====================================================== */


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
        'eme','net_amount'
    ];

    $employee = $this->records['payroll_items'][$sectionIndex]['employees'][$employeeIndex];

    DB::transaction(function () use ($employee, $sectionIndex, $employeeIndex, $fields) {

        PayrollEmeItems::where('id', $employee['id'])->delete();

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
    $payroll = PayrollEme::find($this->payroll_id);

    $emp = DB::table('employee_information as ei')
        ->leftJoin('employee_personal as ep', 'ei.employee_no', '=', 'ep.employee_no')
        ->where('ei.id', $id)
        ->select(
            'ei.id',
            'ei.employee_no',
            'ei.position_id',
            'ei.section_id',
            'ei.employment_type_id',
            DB::raw("CONCAT(COALESCE(ep.firstname,''), ' ', COALESCE(ep.lastname,'')) as name")
        )
        ->first();

    if (!$emp) {
        return;
    }

    // reset duplicate warning
    $this->showDuploicateLabel = false;

    $other_service = new OtherServices;

    // get earnings
    $earnings = $other_service->earnings($emp->employee_no);

    $eme = round(
        floatval(collect($earnings)->firstWhere('code', 'EME')['amount'] ?? 0),
        2
    );


    $net = round($eme, 2);

    /*
    IMPORTANT FIX:
    Your old code only created $data[]
    but never assigned selectedEmployee
    so Livewire could not select anything.
    */

    $this->selectedEmployee = [
        'payroll_id' => $payroll->id,
        'employee_no' => $emp->employee_no,
        'employment_type_id' => $emp->employment_type_id,
        'position_id' => $emp->position_id,
        'section_id' => $emp->section_id,

        'name' => $emp->name,
        'position' => $this->getPositionName($emp->position_id),

        'basic_salary' => 0,
        'eme' => $eme ?? 0,
        'net_amount' => $net ?? 0,
    ];

    // optional: clear results after select
    $this->employeeResults = [];
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

    $exists = PayrollEmeItems::where('payroll_id', $this->payroll_id)
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

        $new = PayrollEmeItems::create([
            'payroll_id' => $this->payroll_id,
            'employee_no' => $this->selectedEmployee['employee_no'],
            'employment_type_id' => $this->selectedEmployee['employment_type_id'],
            'name' => $this->selectedEmployee['name'],
            'position' => $positionName,
            'basic_salary' => $this->selectedEmployee['basic_salary'],
            'eme' => $this->selectedEmployee['eme'],
            'net_amount' => $this->selectedEmployee['net_amount'],
        ]);

       // $this->loadRecords();

        $newItem = $new->toArray();

       
        Log::info('Add employee to salary', ['records' => $newItem]);

        $sectionIndex = $this->findOrCreateSection([
            'section_name' => $sectionName
        ]);

        $this->records['payroll_items'][$sectionIndex]['employees'][] = $newItem;

       $employeeIndex = count($this->records['payroll_items'][$sectionIndex]['employees']) - 1;

        // init fields
        foreach ([
            'eme','net_amount'
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
            PayrollEme::where('id', $this->payroll_id)
                ->update(['status' => 'approved']);

            
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

    

    public function getIsLockedProperty(): bool
    {
        // Payroll is fully locked if approved OR both halves are locked for all employees
        return $this->isApproved;
    }

    public function render()
    {
        return view('livewire.admin.payroll.process.eme');
    }
}
