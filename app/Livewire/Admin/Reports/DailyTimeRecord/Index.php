<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';
    public $monthYear;

    public function mount() {
        $this->monthYear = Carbon::now();
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

        // Return the grouped records to the view
        return view('livewire.admin.reports.daily-time-record.index', [
            'records' => $records,
        ]);
    }


    
}
