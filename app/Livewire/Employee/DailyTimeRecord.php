<?php

namespace App\Livewire\Employee;

use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DailyTimeRecord extends Component
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

    public function mount()
    {
        $this->initializeService();

        try {
            $this->dtrDate = $this->dtrDate ?? now()->subMonth()->format('F, Y');
            $this->employee_id = Auth::user()->employee_no;
            $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);

            if ($this->dailyTimeRecordService) {
                Log::error('DailyTimeRecordService is not null.');
            } else {
                Log::error('DailyTimeRecordService is null while changing month.');
            }

            $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);
        } catch (\Exception $e) {
            $this->errors = [
                'Employee ' . $this->employee_id . ' has no shifting schedule',
                'Employee ' . $this->employee_id . ' has no employee schedule',
            ];
        }
        
    }

    public function changeMonth($increment)
    {
        $this->initializeService();

        Log::info('Before Change Month: ' . $this->dtrDate);
        
        $currentDate = Carbon::createFromFormat('F, Y', $this->dtrDate);
        $currentDate->addMonths($increment);
        
        if ($currentDate->isFuture() || $currentDate->isCurrentMonth()) {
            $this->dtrDate = now()->subMonth()->format('F, Y');
        } else {
            $this->dtrDate = $currentDate->format('F, Y');
        }
        $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_id, $this->dtrDate);
    }

    public function render()
    {
        return view('livewire.employee.daily-time-record');
    }
}
