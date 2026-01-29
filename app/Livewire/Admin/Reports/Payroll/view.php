<?php

namespace App\Livewire\Admin\Reports\Payroll;

use Livewire\Component;
use App\Models\SalaryPayroll;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;


class View extends Component
{
    public SalaryPayroll $payroll;
    public $payrollId;

    public string $filterSalaryMethod = '';

    public array $records = [];
    public bool $isApproved = false;
    public array $updatedItems = [];
    public bool $hasChanges = false;

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
        
        // ✅ FILTER BY SALARY METHOD (from employee_information table)
        if ($this->filterSalaryMethod) {
            $items = $items->filter(function ($item) {
                return $item->information
                    && strcasecmp(
                        $item->information->salary_method,
                        $this->filterSalaryMethod
                    ) === 0;
            });
        }

        $employmentTypes = $items->map(function ($item) {
            return $item->information?->employment_type?->name;
        })->unique()->filter();


        // Group by section (assuming items have 'section_name')
        $sections = $items->groupBy('section_name');

        $payrollItems = [];

        foreach ($sections as $sectionName => $employees) {
            $payrollItems[] = [
                'section_name' => $sectionName,
                'employees' => $employees->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'employee_no' => $item->employee_no,
                        'name' => $item->name,
                        'position' => $item->position,
                        'salary_method' => $item->information?->salary_method ?? 'N/A', // NEW
                        'employment_type' => $item->information?->employment_type?->name ?? 'N/A',
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
                        'mp2' => $item->mp2 ?? 0,
                        'mplstlms' => $item->mplstlms ?? 0,
                        'cir375_cir449' => $item->cir375_cir449 ?? 0,
                        'w_tax' => $item->w_tax ?? 0,
                        'uca' => $item->uca ?? 0,
                        'aut' => $item->aut ?? 0,
                        'total_deductions' => $item->total_deductions ?? 0,
                        'net_amount' => $item->net_amount ?? 0,
                        'net_first_half' => $item->net_first_half ?? 0,
                        'net_second_half' => $item->net_second_half ?? 0,
                        'dbp' => $item->dbp ?? 0,
                        'kawani' => $item->kawani ?? 0,
                        'lbp_payroll_account' => $item->lbp_payroll_account ?? '',
                    ];
                })->toArray(),
            ];
        }

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
            'mp2' => $items->sum('mp2'),
            'mplstlms' => $items->sum('mplstlms'),
            'cir375_cir449' => $items->sum('cir375_cir449'),
            'w_tax' => $items->sum('w_tax'),
            'uca' => $items->sum('uca'),
            'aut' => $items->sum('aut'),
            'dbp' => $items->sum('dbp'),
            'kawani' => $items->sum('kawani'),
            'total_deductions' => $items->sum('total_deductions'),
            'net_amount' => $items->sum('net_amount'),
            'total_deductions' => $items->sum('total_deductions'),
            'lbp_payroll_account' => $items->sum('lbp_payroll_account'),
            'net_first_half' => $items->sum('net_first_half'),
            'net_second_half' => $items->sum('net_second_half'),
        ];

        $this->records = [
            'payroll' => [
                'type' => $this->payroll->salary_method,
                'formatted_payroll_date' => Carbon::parse($this->payroll->payroll_date)->format('M d, Y'),
                'formatted_cutoff_period' => $this->payroll->cut_off_period,
                'formatted_employment_type' => $employmentTypes->implode(', '),
                'no_employees' => $items->pluck('employee_no')->unique()->count(),
                'overall_net_amount' => $items->sum('net_amount'),
                'overall_salary' => $items->sum('gross_amount_earned'),
                'status' => $this->payroll->status,
                'employment_type' => ['id' => 1], // assume government
            ],
            'payroll_items' => $payrollItems,
            'totals' => $totals,
        ];

        $this->isApproved = $this->payroll->status === 'approved';
    }

    public function updatedFilterSalaryMethod()
    {
        $this->prepareRecords();
    }


    public function exportExcel()
    {
        $payrollDate = Carbon::parse($this->payroll->payroll_date)->format('Ymd'); // e.g. 20260116
        $employmentType = $this->records['payroll']['formatted_employment_type']; // fallback if null

        // sanitize employment type for filenames
        $employmentType = str_replace(' ', '_', $employmentType);

        $filename = "payroll-{$employmentType}-{$payrollDate}-" . now()->format('His') . ".xlsx";

        return Excel::download(
            new PayrollExport($this->payroll, $this->filterSalaryMethod),
            $filename
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