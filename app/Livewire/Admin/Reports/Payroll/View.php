<?php

namespace App\Livewire\Admin\Reports\Payroll;

use Livewire\Component;
use App\Models\SalaryPayroll;
use App\Models\PayrollEmeRata;
use App\Models\EmployeeInformation;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use App\Exports\PayrollExportCosJo;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;



class View extends Component
{
    use WithPagination;

    public SalaryPayroll $payroll;
    public $payrollId;

    public string $filterSalaryMethod = '';

    public array $records = [];
    public $model;
    public bool $isApproved = false;
    public array $updatedItems = [];
    public bool $hasChanges = false;

    public string $searchName = '';

    public int $perPage = 10; // default 10 items per page


    public function mount()
    {
        $this->payroll = SalaryPayroll::with([
        'items.information' // ✅ LOAD employee_information
        ])->findOrFail($this->payrollId);

        $this->prepareRecords();
    }

    protected function prepareRecords()
{
    $items = $this->payroll->items;

    // --- Filter by Salary Method ---
    if ($this->filterSalaryMethod) {
        $items = $items->filter(function ($item) {
            return $item->information
                && strcasecmp(
                    $item->information->salary_method,
                    $this->filterSalaryMethod
                ) === 0;
        });
    }

    // --- Filter by Employee Name ---
    if ($this->searchName) {
        $items = $items->filter(function ($item) {
            return str_contains(
                strtolower($item->name),
                strtolower($this->searchName)
            );
        });
    }

    // Get unique employment types
    $employmentTypes = $items->map(function ($item) {
        return $item->information?->employment_type?->name;
    })->unique()->filter();

    $employmentTypesId = $items->map(function ($item) {
        return $item->information?->employment_type?->id;
    })->unique()->filter();

    

    // Group by section
    // Group by section
$sections = $items->groupBy(function ($item) {
    return optional($item->information->section)->name ?? 'NO SECTION';
});
$payrollItems = [];

foreach ($sections as $sectionName => $employees) {

    $sectionTotals = [
        'basic_salary' => $employees->sum('basic_salary'),
        'pera' => $employees->sum('pera'),
        'gross' => $employees->sum('gross_amount_earned'),
        'rlip' => $employees->sum('rlip'),
        'hdmf' => $employees->sum('hdmf'),
        'philhealth' => $employees->sum('philhealth'),
        'consoloan' => $employees->sum('consoloan'),
        'emergency_loan' => $employees->sum('emergency_loan'),
        'plreg' => $employees->sum('plreg'),
        'mpl' => $employees->sum('mpl'),
        'mpl_lite' => $employees->sum('mpl_lite'),
        'cpl' => $employees->sum('cpl'),
        'gsel' => $employees->sum('gsel'),
        'mp2' => $employees->sum('mp2'),
        'mplstlms' => $employees->sum('mplstlms'),
        'cir375_cir449' => $employees->sum('cir375_cir449'),
        'w_tax' => $employees->sum('w_tax'),
        'overpayment' => $employees->sum('overpayment'),
        'tax_3' => $employees->sum('tax_3'),
        'tax_5' => $employees->sum('tax_5'),
        'tax_8' => $employees->sum('tax_8'),
        'tax_10' => $employees->sum('tax_10'),
        'uca' => $employees->sum('uca'),
        'disallowance' => $employees->sum('disallowance'),
        'aut' => $employees->sum('aut'),
        'total_deductions' => $employees->sum('total_deductions'),
        'net_amount' => $employees->sum('net_amount'),
        'dbp' => $employees->sum('dbp'),
        'kawani' => $employees->sum('kawani'),
        'lbp_payroll_account' => $employees->sum('lbp_payroll_account'),
        'net_first_half' => $employees->sum('net_first_half'),
        'net_second_half' => $employees->sum('net_second_half'),
    ];

    $payrollItems[] = [
        'section_name' => $sectionName,
        'employees' => $employees->map(function ($item) {
            return [
                'id' => $item->id,
                'employee_no' => $item->employee_no,
                'name' => $item->name,
                'position' => $item->position,
                'employment_type_id' => $item->information?->employment_type_id,
                'salary_method' => $item->information?->salary_method ?? 'N/A',
                'basic_salary' => $item->basic_salary,
                'pera' => $item->pera ?? 0,
                'gross_amount_earned' => $item->gross_amount_earned,
                'rlip' => $item->rlip ?? 0,
                'hdmf' => $item->hdmf ?? 0,
                'philhealth' => $item->philhealth ?? 0,
                'consoloan' => $item->consoloan ?? 0,
                'emergency_loan' => $item->emergency_loan ?? 0,
                'plreg' => $item->plreg ?? 0,
                'mpl' => $item->mpl ?? 0,
                'mpl_lite' => $item->mpl_lite ?? 0,
                'cpl' => $item->cpl ?? 0,
                'gsel' => $item->gsel ?? 0,
                'mp2' => $item->mp2 ?? 0,
                'mplstlms' => $item->mplstlms ?? 0,
                'cir375_cir449' => $item->cir375_cir449 ?? 0,
                'w_tax' => $item->w_tax ?? 0,
                'overpayment' => $item->overpayment ?? 0,
                'tax_3' => $item->tax_3 ?? 0,
                'tax_5' => $item->tax_5 ?? 0,
                'tax_8' => $item->tax_8 ?? 0,
                'tax_10' => $item->tax_10 ?? 0,
                'uca' => $item->uca ?? 0,
                'disallowance' => $item->disallowance ?? 0,
                'aut' => $item->aut ?? 0,
                'total_deductions' => $item->total_deductions ?? 0,
                'net_amount' => $item->net_amount ?? 0,
                'dbp' => $item->dbp ?? 0,
                'kawani' => $item->kawani ?? 0,
                'lbp_payroll_account' => $item->lbp_payroll_account ?? 0,
                'net_first_half' => $item->net_first_half ?? 0,
                'net_second_half' => $item->net_second_half ?? 0,
            ];
        })->toArray(),
        'section_totals' => $sectionTotals,
    ];
}


    $totalFirstHalf = $items->sum('net_first_half');
    $totalSecondHalf = $items->sum('net_second_half');

    $totals = [
        'basic_salary' => $items->sum('basic_salary'),
        'pera' => $items->sum('pera'),
        'gross' => $items->sum('gross_amount_earned'),
        'rlip' => $items->sum('rlip'),
        'hdmf' => $items->sum('hdmf'),
        'philhealth' => $items->sum('philhealth'),
        'consoloan' => $items->sum('consoloan'),
        'emergency_loan' => $items->sum('emergency_loan'),
        'plreg' => $items->sum('plreg'),
        'mpl' => $items->sum('mpl'),
        'mpl_lite' => $items->sum('mpl_lite'),
        'cpl' => $items->sum('cpl'),
        'gsel' => $items->sum('gsel'),
        'mp2' => $items->sum('mp2'),
        'mplstlms' => $items->sum('mplstlms'),
        'cir375_cir449' => $items->sum('cir375_cir449'),
        'w_tax' => $items->sum('w_tax'),
        'overpayment' => $items->sum('overpayment'),
        'tax_3' => $items->sum('tax_3'),
        'tax_5' => $items->sum('tax_5'),
        'tax_8' => $items->sum('tax_8'),
        'tax_10' => $items->sum('tax_10'),
        'uca' => $items->sum('uca'),
        'disallowance' => $items->sum('disallowance'),
        'aut' => $items->sum('aut'),
        'total_deductions' => $items->sum('total_deductions'),
        'net_amount' => $items->sum('net_amount'),
        'dbp' => $items->sum('dbp'),
        'kawani' => $items->sum('kawani'),
        'lbp_payroll_account' => $items->sum('lbp_payroll_account'),
        'net_first_half' => $totalFirstHalf,
        'net_second_half' => $totalSecondHalf,
        'net_total_by_halves' => $totalFirstHalf + $totalSecondHalf,
    ];

    $this->records = [
        'payroll' => [
            'type' => $this->payroll->salary_method,
            'formatted_payroll_date' => Carbon::parse($this->payroll->payroll_date)->format('M d, Y'),
            'formatted_cutoff_period' => $this->payroll->cut_off_period,
            'formatted_employment_type' => $employmentTypes->implode(', '),
            'condition_employment_type' => $employmentTypesId->implode(', '),
            'no_employees' => $items->pluck('employee_no')->unique()->count(),
            'overall_net_amount' => $items->sum('net_amount'),
            'overall_salary' => $items->sum('gross_amount_earned'),
            'status' => $this->payroll->status,
        ],
        'payroll_items' => $payrollItems,
        'totals' => $totals,
    ];

    $this->isApproved = $this->payroll->status === 'approved';
}

     public function getEmployeeByPosition($positionName)
    {
        $supervisingOfficer = EmployeeInformation::join('employee_personal as ep', 'employee_information.employee_no', '=', 'ep.employee_no')
        ->join('positions as p', 'employee_information.position_id', '=', 'p.id')
        ->where('p.name', $positionName)
        ->selectRaw("p.name as pname, ep.firstname, ep.middlename, ep.lastname, ep.suffix, CONCAT(ep.firstname, ' ', IFNULL(ep.middlename,''), ' ', ep.lastname, ' ', IFNULL(ep.suffix,'')) as full_name")
        ->first();

        if ($supervisingOfficer) {
            return [
                'full_name' => $supervisingOfficer->full_name,
                'position_name' => $supervisingOfficer->pname
            ];
        }

        return [
            'full_name' => 'N/A',
            'position_name' => 'N/A'
        ];
    }

    public function highlightSearchTerm(string $text): string
    {
        if (!$this->searchName) {
            return $text;
        }

        $escapedSearch = preg_quote($this->searchName, '/'); // escape special characters
        return preg_replace(
            "/($escapedSearch)/i",
            '<span class="bg-primary text-white fw-bold">$1</span>',
            $text
        );
    }



    public function updatedFilterSalaryMethod()
    {
       
        $this->prepareRecords();
    }

    public function updatedSearchName()
    {
       
        $this->prepareRecords();
    }


    public function exportExcelCosJo()
{
    // ✅ PRELOAD relationships (VERY IMPORTANT)
    $this->payroll->load([
        'items.information.section'
    ]);

    /**
     * SAFE DATE
     */
    $payrollDate = optional($this->payroll->payroll_date)
        ? Carbon::parse($this->payroll->payroll_date)->format('Ymd')
        : now()->format('Ymd');

    /**
     * SAFE EMPLOYMENT TYPE
     */
    $employmentType =
        $this->records['payroll']['formatted_employment_type']
        ?? 'ALL_EMPLOYEES';

    // sanitize filename (removes special chars)
    $employmentType = Str::slug($employmentType, '_');

    /**
     * OPTIONAL: include salary method in filename
     */
    $salaryMethod = $this->filterSalaryMethod
        ? '-' . Str::slug($this->filterSalaryMethod, '_')
        : '';

    /**
     * FINAL FILENAME
     */
    $filename = "PAYROLL_{$employmentType}{$salaryMethod}_{$payrollDate}_" .
        now()->format('His') . ".xlsx";

    $supervisingOfficer = $this->getEmployeeByPosition('Supervising Administrative Officer');  
    $chiefadministrativeOfficer = $this->getEmployeeByPosition('Chief Administrative Officer'); 
    
   

    return Excel::download(
        new PayrollExportCosJo(
            $this->payroll,
            $this->filterSalaryMethod,
            $this->searchName,
            $supervisingOfficer,
            $chiefadministrativeOfficer
        ),
        $filename
    );
}


public function exportExcel()
{
    // ✅ PRELOAD relationships (VERY IMPORTANT)
    $this->payroll->load([
        'items.information.section'
    ]);

    /**
     * SAFE DATE
     */
    $payrollDate = optional($this->payroll->payroll_date)
        ? Carbon::parse($this->payroll->payroll_date)->format('Ymd')
        : now()->format('Ymd');

    /**
     * SAFE EMPLOYMENT TYPE
     */
    $employmentType =
        $this->records['payroll']['formatted_employment_type']
        ?? 'ALL_EMPLOYEES';

    // sanitize filename (removes special chars)
    $employmentType = Str::slug($employmentType, '_');

    /**
     * OPTIONAL: include salary method in filename
     */
    $salaryMethod = $this->filterSalaryMethod
        ? '-' . Str::slug($this->filterSalaryMethod, '_')
        : '';

    /**
     * FINAL FILENAME
     */
    $filename = "PAYROLL_{$employmentType}{$salaryMethod}_{$payrollDate}_" .
        now()->format('His') . ".xlsx";

    $supervisingOfficer = $this->getEmployeeByPosition('Supervising Administrative Officer');  
    $chiefadministrativeOfficer = $this->getEmployeeByPosition('Chief Administrative Officer'); 
    
   

    return Excel::download(
        new PayrollExport(
            $this->payroll,
            $this->filterSalaryMethod,
            $this->searchName,
            $supervisingOfficer,
            $chiefadministrativeOfficer
        ),
        $filename
    );
}


    protected function paginateArray(array $items, int $perPage = 10, int $page = null, array $options = [])
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage();
        $items = collect($items);
        $paginatedItems = $items->slice(($page - 1) * $perPage, $perPage)->values();
        return new LengthAwarePaginator(
            $paginatedItems,
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }

    public function render()
    {
      

        return view('livewire.admin.reports.payroll.view', [
            'records' => $this->records,
            'isApproved' => $this->isApproved,
            'updatedItems' => $this->updatedItems,
            'hasChanges' => $this->hasChanges,
        ]);
    }
}