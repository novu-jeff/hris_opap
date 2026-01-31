<?php

namespace App\Livewire\Admin\Reports\Payroll;

use Illuminate\Database\Eloquent\Builder;

use Livewire\Component;
use App\Models\SalaryPayroll;
use App\Models\EmployementTypes;
use Livewire\WithPagination;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;

use Illuminate\Support\Facades\DB;


class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $cutoffPeriod;
    public $salaryMethod;
    public $perPage = 10;

    public $employmentTypes;
    public $filterEmploymentType = ''; // bound to dropdown

    public $salaryMethods = ['Land Bank ATM', 'Check', 'Cash'];
    public $cutoffPeriods = ['01 to 15', '16 to end'];

    public $search;

    protected $updatesQueryString = ['cutoffPeriod', 'salaryMethod', 'search'];

    protected $listeners = ['remove'];

    public function updatingCutoffPeriod() { $this->resetPage(); }
    public function updatingSalaryMethod() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function remove(bool $isNotify = true, ?int $id = null)
    {
        if ($isNotify) {

            $this->selected_id = $id;

            $this->dispatch('showConfirmation', [
                'title'   => 'Delete Payroll?',
                'message' => 'This will permanently delete the payroll and ALL its salary items. This action cannot be undone.',
                'action'  => 'remove'
            ]);

            return;
        }

        DB::transaction(function () {

            $payroll = SalaryPayroll::with('items')
                ->findOrFail($this->selected_id);

            // 🔥 Delete salary items first
            $payroll->items()->delete();

            // 🔥 Delete payroll
            $payroll->delete();
        });

        $this->dispatch('alert', [
            'status' => 'success',
            'title'  => 'Deleted!',
            'message'=> 'Payroll and salary items deleted successfully.'
        ]);

        $this->resetPage();
        $this->selected_id = null;
    }

    public function approve($id)
    {
        $payroll = SalaryPayroll::findOrFail($id);

        if ($payroll->status === 'approved') {
            return;
        }

        $payroll->update([
            'status' => 'approved'
        ]);

        $this->dispatch('alert', [
            'status' => 'success',
            'title'  => 'Approved',
            'message'=> 'Payroll has been approved successfully.'
        ]);
    }

public function disapprove($id)
    {
    $payroll = SalaryPayroll::findOrFail($id);

    if ($payroll->status === 'pending') {
        return;
    }

    $payroll->update([
        'status' => 'pending'
    ]);

    $this->dispatch('alert', [
        'status' => 'warning',
        'title'  => 'Disapproved',
        'message'=> 'Payroll has been disapproved.'
    ]);
}


    public function downloadPayroll($payrollId)
    {
        $payroll = SalaryPayroll::with([
            'items.information.employment_type'
        ])->findOrFail($payrollId);

        $employmentTypes = $payroll->items
        ->map(fn ($item) => $item->information?->employment_type?->name)
        ->filter()
        ->unique()
        ->implode('-'); // use dash for filename safety

    $employmentTypes = $employmentTypes ?: 'ALL';

    $fileName = 'Payroll_' . $employmentTypes . '_' .
        \Carbon\Carbon::parse($payroll->payroll_date)->format('Ymd') . '.xlsx';

        return Excel::download(
            new PayrollExport($payroll, $this->salaryMethod),
            $fileName
        );
    }


    public function render()
    {
        
        $this->employmentTypes = EmployementTypes::all(); // fetch all types
    
       $query = SalaryPayroll::withCount('items')
            ->withSum('items', 'net_amount')
            ->with(['items.information'])
            ->orderBy('payroll_date', 'desc');

        // Filter by employment type
        if ($this->filterEmploymentType) {
            $query->whereHas('items.information', function ($q) {
                $q->where('employment_type_id', $this->filterEmploymentType);
            });
        }

        // Filter by cutoff period
        if ($this->cutoffPeriod) {
            if ($this->cutoffPeriod === '01 to 15') {
                $query->where('cut_off_period', 'like', '%-01 to %-15%');
            }
            if ($this->cutoffPeriod === '16 to end') {
                $query->where('cut_off_period', 'like', '%-16 to %-31%');
            }
        }

        // Filter by search
        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                // Search in payroll fields
                $q->where('id', 'like', "%{$search}%")
                ->orWhere('batch_id', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");

                // Search in related items
                $q->orWhereHas('items', function ($iq) use ($search) {
                    $iq->where('employee_no', 'like', "%{$search}%")
                    ->orWhereHas('information.employment_type', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%");
                    });
                });
            });
        }


        $payrolls = $query->paginate($this->perPage);

        /**
         * Compute totals PER PAYROLL (cutoff-aware)
         */
        $payrolls->getCollection()->transform(function ($payroll) {

                // Collect unique employment types from items
                $employmentTypes = $payroll->items->map(function ($item) {
                    return $item->information->employment_type->name ?? null;
                })->unique()->filter(); // remove nulls

                $payroll->employment_types = $employmentTypes->implode(', ');

                // Count unique employees
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