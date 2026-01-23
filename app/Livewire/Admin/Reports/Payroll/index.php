<?php

namespace App\Livewire\Admin\Reports\Payroll;

use Livewire\Component;
use App\Models\SalaryPayroll;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $cutoffPeriod;
    public $salaryMethod;
    public $perPage = 10;

    public $salaryMethods = ['Land Bank ATM', 'Check', 'Cash'];
    public $cutoffPeriods = ['01 to 15', '16 to end'];

    public $search;

    protected $updatesQueryString = ['cutoffPeriod', 'salaryMethod', 'search'];

    public function updatingCutoffPeriod() { $this->resetPage(); }
    public function updatingSalaryMethod() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $query = SalaryPayroll::withCount('items')
            ->withSum('items', 'net_amount') 
            ->orderBy('payroll_date', 'desc');

        if ($this->cutoffPeriod) {
            $query->where('cut_off_period', $this->cutoffPeriod);
        }

        if ($this->salaryMethod) {
            $query->where('salary_method', $this->salaryMethod);
        }

        if ($this->search) {
            $query->where('id', 'like', "%{$this->search}%")
                ->orWhere('batch_id', 'like', "%{$this->search}%")
                ->orWhere('status', 'like', "%{$this->search}%");
        }

        $payrolls = $query->paginate($this->perPage);

        /**
         * Compute totals PER PAYROLL (cutoff-aware)
         */
        $payrolls->getCollection()->transform(function ($payroll) {

            $isFirstHalf = false;

            if ($payroll->cut_off_period) {
                [$start] = explode(' to ', $payroll->cut_off_period);
                $isFirstHalf = Carbon::parse(trim($start))->day <= 15;
            }

            $totalNet = 0;
            $totalSalary = 0;

            foreach ($payroll->items as $item) {
                $amount = $isFirstHalf
                    ? (float) $item->net_first_half
                    : (float) $item->net_second_half;

                $totalNet += $amount;
                $totalSalary += $amount;
            }

            $payroll->total_net_amount = round($totalNet, 2);
            $payroll->total_salary_amount = round($totalSalary, 2);

            $payroll->employee_count = $payroll->items
                ->pluck('employee_no')
                ->unique()
                ->count();

            return $payroll;
        });

        return view('livewire.admin.reports.payroll.index', [
            'payrolls' => $payrolls,
            'cutOffPeriods' => $this->cutoffPeriods,
            'salaryMethods' => $this->salaryMethods,
        ]);
    }
}