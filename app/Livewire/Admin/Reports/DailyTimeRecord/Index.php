<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\EmployeeTimelogs;
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

    public function mount() {
        $this->monthYear = Carbon::now();
        $this->employmentTypes = EmployementTypes::all();
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
