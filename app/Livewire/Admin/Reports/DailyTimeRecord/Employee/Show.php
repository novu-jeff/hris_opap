<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord\Employee;

use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Show extends Component
{
    public $records;
    public $dtr = null;
    public $dtrDate;
    public $employee_id;
    public $errors;

    protected $dailyTimeRecordService;

    public function initializeService()
    {
        $this->dailyTimeRecordService = app(DailyTimeRecordService::class);
    }

    public function mount($id, $date)
    {
        $this->initializeService();

        try {
            $this->dtrDate = $date;
            $this->employee_id = $id;
            $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);
        } catch (\Exception $e) {
            $this->errors = explode("\n", $e->getMessage());
        }
        
    }

    public function changeMonth($increment)
    {
        $this->initializeService();

        Log::info('Before Change Month: ' . $this->dtrDate);
        
        $currentDate = Carbon::createFromFormat('F, Y', $this->dtrDate);
        $currentDate->addMonths($increment);
        
        if ($currentDate->isFuture() || $currentDate->isCurrentMonth()) {
            $this->dtrDate = now()->format('F, Y');
        } else {
            $this->dtrDate = $currentDate->format('F, Y');
        }
        $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);
    }

    public function render()
    {
        return view('livewire.admin.reports.daily-time-record.employee.show');
    }
}
