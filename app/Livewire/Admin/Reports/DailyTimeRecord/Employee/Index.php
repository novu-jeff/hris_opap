<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord\Employee;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Index extends Component
{
    public $date;
    public $records;
    public $dtr = null;
    public $dtrDate;
    public $employee_id;

    protected $dailyTimeRecordService;

    protected $listeners = ['updateDate'];

    public function initializeService()
    {
        $this->dailyTimeRecordService = app(DailyTimeRecordService::class);
    }

    public function mount($date)
    {
        $this->date = $date ?? now()->format('F, Y');
        $this->dtrDate = $date ?? now()->format('F, Y');
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $this->records = EmployeeAccount::with('personal')->get();
    }

    public function updateDate($newDate)
    {
        $this->dtrDate = $newDate;
        $this->loadRecords();
    }

    public function changeMonth($increment, $employee_id)
    {
        $this->initializeService();

        Log::info('Before Change Month: ' . $this->dtrDate);
    
        $currentDate = Carbon::createFromFormat('F, Y', $this->dtrDate);
        $currentDate->addMonths($increment);
    
        $this->dtrDate = $currentDate->format('F, Y');
    
        $this->employee_id = $employee_id;
        
        $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);
    }
    
    public function showDtr($id) {
        
        $this->initializeService();

        $this->employee_id = $id;
        $this->dtrDate = $this->date ?? now()->format('F, Y'); // change date to selected date or current

        $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);

        // Dispatch the modal
        $this->dispatch('showModal', [
            'modal' => 'dtrModal'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.reports.daily-time-record.employee.index');
    }
}
