<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord;

use App\Models\EmployeeAccount;
use App\Models\EmployeeTimelogs;
use App\Models\EmployeeInformation;
use App\Models\EmployementTypes;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selectedType;
    public $entries = 10;
    public $search = '';
    public $monthYear;
    public $employmentTypes;
    public $selectedMonth;
    public $selectedEmployees = [];
    public $selectAll = false;
    

    public function mount() {
        $this->monthYear = Carbon::now();
        //$this->employmentTypes = EmployementTypes::all();
        $this->employmentTypes = EmployementTypes::orderByRaw("
                FIELD(code,
                    'RC',
                    'COS',
                    'COS2',
                    'JO',
                    'RC2'
                )
            ")->get();
        $this->selectedMonth = now()->format('Y-m');
    }

    public function updatedSelectedEmployees()
{
    logger()->info('Selected Employees', $this->selectedEmployees);
}
    
    public function updatedSelectAll($value)
    {
        if ($value) {

            $query = EmployeeInformation::query();

            if ($this->selectedType !== null) {
                if ($this->selectedType === 'unassigned') {
                    $query->whereNull('employment_type_id')
                    ->where('isDeleted', false)
                    ->where('status', 'active');
                } else {
                    $query->where('employment_type_id', $this->selectedType)
                    ->where('isDeleted', false)
                    ->where('status', 'active');
                }
            }

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('employee_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('personal', function ($subQuery) {
                        $subQuery->whereRaw(
                            "CONCAT(firstname,' ',lastname) LIKE ?",
                            ['%' . $this->search . '%']
                        );
                    });
                });
            }

            $this->selectedEmployees = $query
                ->pluck('employee_no')
                ->toArray();

        } else {

            $this->selectedEmployees = [];
        }
    }

    public function downloadAllDtr()
    {
        if (empty($this->selectedEmployees)) {
            session()->flash('error', 'Please select employees.');
            return;
        }
    
        $date = Carbon::parse($this->selectedMonth);

        return redirect()->route(
            'dtr.download-all',
            [
                'month' => $date->format('F'),
                'year'  => $date->format('Y'),
                'employees' => implode(',', $this->selectedEmployees)
            ]
        );
    }

    public function render()
    {
        $query = EmployeeInformation::with('personal');

        if ($this->selectedType !== null) {
            if ($this->selectedType === 'unassigned') {
                $query->whereNull('employment_type_id');
            } else {
                $query->where('employment_type_id', $this->selectedType);
            }
        }

        if ($this->search) {

            $this->resetPage();

            $query->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function ($subQuery) {
                    $subQuery->whereRaw(
                        "CONCAT(firstname, ' ', lastname) LIKE ?",
                        ['%' . $this->search . '%']
                    );
                });
            });
        }

        return view('livewire.admin.reports.daily-time-record.index', [
            'records' => $query->paginate($this->entries),
        ]);
    }
}
