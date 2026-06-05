<?php

namespace App\Livewire\Admin\Reports\Payroll\Ot;

use Livewire\Component;
use App\Models\OTPayroll;
use App\Models\EmployeeInformation;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExportOt;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;



class View extends Component
{
    use WithPagination;

    public OTPayroll $payroll;
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
        $this->payroll = OTPayroll::with([
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
                    'basic_salary' =>
                    $employees->sum('basic_salary'),
                    'tax' =>
                        $employees->sum('tax'),
                    'amount' =>
                        $employees->sum('amount'),
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

                    'basic_salary' => $item->basic_salary,

                    'duration' => $item->duration ?? 0,

                    'amount' => $item->amount ?? 0,

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

            'basic_salary' =>
                $items->sum('basic_salary'),

            'amount' =>
                $items->sum('amount'),
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
    
                'formatted_payroll_date' => $this->payroll->period,
    
                'formatted_employment_type' => $employmentTypes->implode(', '),
    
                'condition_employment_type' => $employmentTypesId->implode(','),
    
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


    public function exportExcelOt()
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
        new PayrollExportOt(
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
      

        return view('livewire.admin.reports.payroll.ot.view', [
            'records' => $this->records,
            'isApproved' => $this->isApproved,
            'updatedItems' => $this->updatedItems,
            'hasChanges' => $this->hasChanges,
        ]);
    }
}