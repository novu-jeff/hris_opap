<?php

namespace App\Livewire\Admin\Reports\Payroll\Premium;

use Illuminate\Database\Eloquent\Builder;

use Livewire\Component;
use App\Models\BonusPayroll;
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

    public $salaryMethods = ['Land Bank ATM', 'Check', 'Cash', 'Hold', 'UnHold'];
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

            $payroll = BonusPayroll::with('items')
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
        $payroll = BonusPayroll::findOrFail($id);

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
    $payroll = BonusPayroll::findOrFail($id);

    if ($payroll->status === 'pending') {
        return;
    }

    $payroll->update([
        'status' => 'pending'
    ]);

    $this->dispatch('alert', [
        'status' => 'warning',
        'title'  => 'Disapproved',
        'message'=> 'EME Payroll has been disapproved.'
    ]);
}


    public function downloadPayroll($payrollId)
    {
        $payroll = BonusPayroll::with([
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
    $this->employmentTypes =
        EmployementTypes::all();

    /*
    |--------------------------------------------------------------------------
    | Premium Payroll Query
    |--------------------------------------------------------------------------
    */

    $query = BonusPayroll::withCount('items')

        ->withSum('items', 'net_amount')

        ->with([
            'items.information',
            'employment_type'
        ])

        ->where('bonus_type', 'premium')

        ->where('status', 'approved')

        ->orderBy('coverage_from', 'desc')

        ->orderBy('payroll_date', 'desc');

    /*
    |--------------------------------------------------------------------------
    | Employment Type Filter
    |--------------------------------------------------------------------------
    */

    if ($this->filterEmploymentType) {

        $query->where(
            'employment_type',
            $this->filterEmploymentType
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($this->search) {

        $search = $this->search;

        $query->where(function ($q)
        use ($search) {

            $q->where(
                'id',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'batch_id',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'semester',
                'like',
                "%{$search}%"
            )

            ->orWhereHas(
                'items',
                function ($iq)
                use ($search) {

                    $iq->where(
                        'employee_no',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'name',
                        'like',
                        "%{$search}%"
                    );

                }
            );

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Get Payrolls
    |--------------------------------------------------------------------------
    */

    $payrolls = $query->get();

    /*
    |--------------------------------------------------------------------------
    | Transform Records
    |--------------------------------------------------------------------------
    */

    $payrolls->transform(function ($payroll) {

        /*
        |--------------------------------------------------------------------------
        | Employment Type
        |--------------------------------------------------------------------------
        */

        $payroll->employment_types =
            $payroll->employment_type->name
            ?? 'N/A';

        /*
        |--------------------------------------------------------------------------
        | Employee Count
        |--------------------------------------------------------------------------
        */

        $payroll->employee_count =
            $payroll->items
                ->pluck('employee_no')
                ->unique()
                ->count();

        /*
        |--------------------------------------------------------------------------
        | Semester Label
        |--------------------------------------------------------------------------
        */

        $payroll->formatted_semester =
            strtoupper(
                str_replace(
                    '_',
                    ' ',
                    $payroll->semester
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Coverage Label
        |--------------------------------------------------------------------------
        */

        $payroll->formatted_coverage =
            Carbon::parse(
                $payroll->coverage_from
            )->format('M d, Y')

            . ' - ' .

            Carbon::parse(
                $payroll->coverage_to
            )->format('M d, Y');

        /*
        |--------------------------------------------------------------------------
        | Payroll Date
        |--------------------------------------------------------------------------
        */

        $payroll->formatted_payroll_date =
            Carbon::parse(
                $payroll->payroll_date
            )->format('F d, Y');

        return $payroll;
    });

    /*
    |--------------------------------------------------------------------------
    | Group By Coverage Year
    |--------------------------------------------------------------------------
    */

    $groupedPayrolls =
        $payrolls->groupBy(function ($payroll) {

            return Carbon::parse(
                $payroll->coverage_from
            )->format('F Y');

        });

    /*
    |--------------------------------------------------------------------------
    | Return View
    |--------------------------------------------------------------------------
    */

    return view(
        'livewire.admin.reports.payroll.premium.index',
        [

            'groupedPayrolls' =>
                $groupedPayrolls,

            'cutOffPeriods' =>
                $this->cutoffPeriods,

            'salaryMethods' =>
                $this->salaryMethods,

        ]
    );
}
}