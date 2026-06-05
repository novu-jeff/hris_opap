<?php

namespace App\Livewire\Admin\Reports\Payroll\Ot;

use Illuminate\Database\Eloquent\Builder;

use Livewire\Component;
use App\Models\OTPayroll;
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

            $payroll = OTPayroll::with('items')
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
        $payroll = OTPayroll::findOrFail($id);

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
    $payroll = OTPayroll::findOrFail($id);

    if ($payroll->status === 'pending') {
        return;
    }

    $payroll->update([
        'status' => 'pending'
    ]);

    $this->dispatch('alert', [
        'status' => 'warning',
        'title'  => 'Disapproved',
        'message'=> 'Overtime Payroll has been disapproved.'
    ]);
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

    $query = OTPayroll::withCount('items')
    ->withSum('items', 'net_amount')
    ->with([
        'items.information',
        'employment_types'
    ])
    ->where('status', 'approved')
    ->orderBy('created_at', 'desc');

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

        $payroll->employment_type_name =
    $payroll->employment_types?->name ?? 'N/A';

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

        

        return $payroll;
    });

    /*
    |--------------------------------------------------------------------------
    | Group By Coverage Year
    |--------------------------------------------------------------------------
    */

    $groupedPayrolls = $payrolls->groupBy(function ($payroll) {

        [$start] = explode(' to ', $payroll->period);
    
        return Carbon::parse(trim($start))
            ->format('F Y');
    
    });

    /*
    |--------------------------------------------------------------------------
    | Return View
    |--------------------------------------------------------------------------
    */

    return view(
        'livewire.admin.reports.payroll.ot.index',
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