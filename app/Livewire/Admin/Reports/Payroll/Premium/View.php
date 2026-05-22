<?php

namespace App\Livewire\Admin\Reports\Payroll\Premium;

use Livewire\Component;
use App\Models\BonusPayroll;
use App\Models\EmployeeInformation;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExportPremium;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;



class View extends Component
{
    use WithPagination;

    public BonusPayroll $payroll;
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
        $this->payroll = BonusPayroll::with([
        'items.information' // ✅ LOAD employee_information
        ])->findOrFail($this->payrollId);

        $this->prepareRecords();
    }

    protected function prepareRecords()
    {
        $items = $this->payroll->items;
    
        /*
        |--------------------------------------------------------------------------
        | FILTER BY SALARY METHOD
        |--------------------------------------------------------------------------
        */
    
        if ($this->filterSalaryMethod) {
    
            $items = $items->filter(function ($item) {
    
                return $item->information
                    && strcasecmp(
                        $item->information->salary_method,
                        $this->filterSalaryMethod
                    ) === 0;
    
            });
    
        }
    
        /*
        |--------------------------------------------------------------------------
        | FILTER BY EMPLOYEE NAME
        |--------------------------------------------------------------------------
        */
    
        if ($this->searchName) {
    
            $items = $items->filter(function ($item) {
    
                return str_contains(
                    strtolower($item->name),
                    strtolower($this->searchName)
                );
    
            });
    
        }
    
        /*
        |--------------------------------------------------------------------------
        | EMPLOYMENT TYPES
        |--------------------------------------------------------------------------
        */
    
        $employmentTypes = $items->map(function ($item) {
    
            return $item->information?->employment_type?->name;
    
        })->unique()->filter();
    
        $employmentTypesId = $items->map(function ($item) {
    
            return $item->information?->employment_type?->id;
    
        })->unique()->filter();
    
        /*
        |--------------------------------------------------------------------------
        | GROUP BY SECTION
        |--------------------------------------------------------------------------
        */
    
        $sections = $items->groupBy(function ($item) {
    
            return optional($item->information->section)->name
                ?? 'NO SECTION';
    
        });
    
        $payrollItems = [];
    
        foreach ($sections as $sectionName => $employees) {
    
            $sectionTotals = [

                /*
    |--------------------------------------------------------------------------
    | MONTHLY TOTALS
    |--------------------------------------------------------------------------
    */

         'january_amount' =>
            $employees->sum('january_amount'),

        'february_amount' =>
            $employees->sum('february_amount'),

        'march_amount' =>
            $employees->sum('march_amount'),

        'april_amount' =>
            $employees->sum('april_amount'),

        'may_amount' =>
            $employees->sum('may_amount'),

        'june_amount' =>
            $employees->sum('june_amount'),

        'july_amount' =>
            $employees->sum('july_amount'),

        'august_amount' =>
            $employees->sum('august_amount'),

        'september_amount' =>
            $employees->sum('september_amount'),

        'october_amount' =>
            $employees->sum('october_amount'),

        'november_amount' =>
            $employees->sum('november_amount'),

        'december_amount' =>
            $employees->sum('december_amount'),
    
                /*
    |--------------------------------------------------------------------------
    | TOTALS
    |--------------------------------------------------------------------------
    */

        'basic_salary' =>
        $employees->sum('basic_salary'),

        'total_amount' =>
            $employees->sum('total_amount'),

        'bonus' =>
            $employees->sum('bonus'),

        'tax' =>
            $employees->sum('tax'),

        'net_amount' =>
            $employees->sum('net_amount'),
    
    ];
    
            $payrollItems[] = [
    
                'section_name' => $sectionName,
    
                'employees' => $employees->map(function ($item) {

                return [

                    'id' => $item->id,

                    'employee_no' => $item->employee_no,

                    'name' => $item->name,

                    'position' => $item->position,

                    'date_hired' => $item->date_hired,

                    'basic_salary' => $item->basic_salary,

                    /*
                    |--------------------------------------------------------------------------
                    | MONTHLY PREMIUMS
                    |--------------------------------------------------------------------------
                    */

                    'january_amount' => $item->january_amount ?? 0,
                    'february_amount' => $item->february_amount ?? 0,
                    'march_amount' => $item->march_amount ?? 0,
                    'april_amount' => $item->april_amount ?? 0,
                    'may_amount' => $item->may_amount ?? 0,
                    'june_amount' => $item->june_amount ?? 0,

                    'july_amount' => $item->july_amount ?? 0,
                    'august_amount' => $item->august_amount ?? 0,
                    'september_amount' => $item->september_amount ?? 0,
                    'october_amount' => $item->october_amount ?? 0,
                    'november_amount' => $item->november_amount ?? 0,
                    'december_amount' => $item->december_amount ?? 0,

                    /*
                    |--------------------------------------------------------------------------
                    | TOTALS
                    |--------------------------------------------------------------------------
                    */

                    'total_amount' => $item->total_amount ?? 0,

                    'percentage' => $item->percentage ?? 0,

                    'bonus' => $item->bonus ?? 0,

                    'tax' => $item->tax ?? 0,

                    'net_amount' => $item->net_amount ?? 0,

                ];

            })->toArray(),
    
                'section_totals' => $sectionTotals,
    
            ];
        }
    
        /*
        |--------------------------------------------------------------------------
        | GRAND TOTALS
        |--------------------------------------------------------------------------
        */
    
        $totals = [
    
             /*
    |--------------------------------------------------------------------------
    | MONTHLY TOTALS
    |--------------------------------------------------------------------------
    */

    'january_amount' =>
        $items->sum('january_amount'),

    'february_amount' =>
        $items->sum('february_amount'),

    'march_amount' =>
        $items->sum('march_amount'),

    'april_amount' =>
        $items->sum('april_amount'),

    'may_amount' =>
        $items->sum('may_amount'),

    'june_amount' =>
        $items->sum('june_amount'),

    'july_amount' =>
        $items->sum('july_amount'),

    'august_amount' =>
        $items->sum('august_amount'),

    'september_amount' =>
        $items->sum('september_amount'),

    'october_amount' =>
        $items->sum('october_amount'),

    'november_amount' =>
        $items->sum('november_amount'),

    'december_amount' =>
        $items->sum('december_amount'),

    /*
    |--------------------------------------------------------------------------
    | OVERALL TOTALS
    |--------------------------------------------------------------------------
    */

    'basic_salary' =>
        $items->sum('basic_salary'),

    'total_amount' =>
        $items->sum('total_amount'),

    'bonus' =>
        $items->sum('bonus'),

    'tax' =>
        $items->sum('tax'),

    'net_amount' =>
        $items->sum('net_amount'),
    
        ];
    
        /*
        |--------------------------------------------------------------------------
        | RECORDS
        |--------------------------------------------------------------------------
        */
    
        $this->records = [
    
            'payroll' => [
    
                'type' => $this->payroll->bonus_type,
    
                'bonus_type' => $this->payroll->bonus_type,
    
                'formatted_payroll_date' => Carbon::parse(
                    $this->payroll->payroll_date
                )->format('M d, Y'),
    
                'formatted_employment_type' => $employmentTypes->implode(', '),
    
                'condition_employment_type' => $employmentTypesId->implode(','),
    
                'coverage_from' => $this->payroll->coverage_from,
    
                'coverage_to' => $this->payroll->coverage_to,
    
                'semester' => $this->payroll->semester,
    
                'percentage' => $this->payroll->percentage,
    
                'no_employees' => $items
                    ->pluck('employee_no')
                    ->unique()
                    ->count(),
    
                'overall_net_amount' => $items->sum('net_amount'),
    
                'status' => $this->payroll->status,
    
            ],
    
            'payroll_items' => $payrollItems,
    
            'totals' => $totals,
    
        ];
    
        $this->isApproved =
            $this->payroll->status === 'approved';
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


    public function exportExcelPremium()
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
        new PayrollExportPremium(
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
        new PayrollExportMidYear(
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
      

        return view('livewire.admin.reports.payroll.premium.view', [
            'records' => $this->records,
            'isApproved' => $this->isApproved,
            'updatedItems' => $this->updatedItems,
            'hasChanges' => $this->hasChanges,
        ]);
    }
}