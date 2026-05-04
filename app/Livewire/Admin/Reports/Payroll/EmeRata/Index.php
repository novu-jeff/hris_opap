<?php

namespace App\Livewire\Admin\Reports\Payroll\EmeRata;

use Illuminate\Database\Eloquent\Builder;

use Livewire\Component;
use App\Models\SalaryPayroll;
use App\Models\PayrollEmeRata;
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

            $payroll = PayrollEmeRata::with('items')
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
        $payroll = PayrollEmeRata::findOrFail($id);

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
    $payroll = PayrollEmeRata::findOrFail($id);

    if ($payroll->status === 'pending') {
        return;
    }

    $payroll->update([
        'status' => 'pending'
    ]);

    $this->dispatch('alert', [
        'status' => 'warning',
        'title'  => 'Disapproved',
        'message'=> 'EME RATAPayroll has been disapproved.'
    ]);
}


    public function downloadPayroll($payrollId)
    {
        $payroll = PayrollEmeRata::with([
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
    $this->employmentTypes = EmployementTypes::all();

    $query = PayrollEmeRata::withCount('items')
        ->withSum('items', 'net_amount')
        ->with(['items.information'])
        ->where('status', 'approved')
        ->orderBy('payroll_date', 'desc');

    if ($this->filterEmploymentType) {
        $query->whereHas('items.information', function ($q) {
            $q->where('employment_type_id', $this->filterEmploymentType);
        });
    }

    if ($this->search) {
        $search = $this->search;

        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
                ->orWhere('batch_id', 'like', "%{$search}%")
                ->orWhereHas('items', function ($iq) use ($search) {
                    $iq->where('employee_no', 'like', "%{$search}%");
                });
        });
    }

    $payrolls = $query->get();

    $payrolls->transform(function ($payroll) {
        $employmentTypes = $payroll->items
            ->map(fn($item) => $item->information->employment_type->name ?? null)
            ->unique()
            ->filter();

        $payroll->employment_types = $employmentTypes->implode(', ');

        $payroll->employee_count = $payroll->items
            ->pluck('employee_no')
            ->unique()
            ->count();

        return $payroll;
    });

    $groupedPayrolls = $payrolls->groupBy(function ($payroll) {
        return Carbon::parse($payroll->payroll_date)->format('F Y');
    });

    return view('livewire.admin.reports.payroll.eme-rata.index', [
        'groupedPayrolls' => $groupedPayrolls,
        'cutOffPeriods' => $this->cutoffPeriods,
        'salaryMethods' => $this->salaryMethods,
    ]);
}
}