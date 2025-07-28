<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord\Employee;

use App\Models\EmployeeAccount;
use App\Models\EmployeeTimelogs;
use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $month;
    public $year;

    public $dtr = null;
    public $dtrDate;
    public $employee_id;
    public $isLoading;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    protected $dailyTimeRecordService;

    protected $listeners = ['updateDate'];

    public function initializeService()
    {
        $this->dailyTimeRecordService = app(DailyTimeRecordService::class);
    }

    public function mount($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function updateDate($newDate)
    {
        $this->dtrDate = $newDate;
    }

    public function render()
    {

        $model = EmployeeAccount::with('personal');

        if ($this->search) {

            $this->resetPage();
    
            $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('personal', function($subQuery) {
                        $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                    });
            });
        }

        $records = $model->paginate($this->entries);

        return view('livewire.admin.reports.daily-time-record.employee.index', [
            'records' => $records
        ]);
    }
}
