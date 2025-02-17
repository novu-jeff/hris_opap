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
    public $officialTime;
    public $monthDate;
    public $employee_no;
    public $errors;

    protected $dailyTimeRecordService;

    public function initializeService()
    {
        $this->dailyTimeRecordService = app(DailyTimeRecordService::class);
    }

    public function mount($month, $year)
    {
        
        $this->initializeService();


        try {
            $this->dtrDate = Carbon::parse($month . ' ' . $year);
            $this->monthDate = $this->dtrDate->format('Y-m');
            $this->officialTime = Carbon::now()->format('h:i A');
            $this->employee_no = Auth::user()->employee_no;
            $this->dtr = $this->dailyTimeRecordService->getDailyTimeRecord($this->employee_no, $this->dtrDate);
        } catch (\Exception $e) {
            $this->errors = array_merge($this->errors ?? [], explode("\n", trim($e->getMessage())));
        }
        
    }

    public function changeMonth($action, $value = null)
    {
        
        if($action == 'control') {
            $currentDate = $this->dtrDate;
            $currentDate = $currentDate->addMonths($value);
        }

        if($action == 'date') {
            $currentDate = Carbon::parse($this->monthDate);
        }

        return redirect()->route('employee.dtr', [
            'id' => $this->employee_no,
            'month' => $currentDate->format('F'),
            'year' => $currentDate->format('Y')
        ]);
    }

    public function render()
    {
        return view('livewire.employee.daily-time-record');
    }
}
