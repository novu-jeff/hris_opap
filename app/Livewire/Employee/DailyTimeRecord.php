<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeInformation;
use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DailyTimeRecord extends Component
{
    public $records;
    public $logs = null;
    public $dtrDate;
    public $officialTime;
    public $monthDate;
    public $employee_no;
    public $errors;

    protected $timeLogService;

    public function initializeService()
    {
        $this->timeLogService = app(TimeLogService::class);
    }

    public function mount($month, $year)
    {
        
        $this->initializeService();


        try {

            $employee_no = Auth::user()->employee_no;

            $this->dtrDate = Carbon::parse($month . ' ' . $year);
            $this->monthDate = $this->dtrDate->format('Y-m');
            $this->officialTime = Carbon::now()->format('h:i A');
            $this->employee_no = $employee_no;

            $data = $this->getEmployeeInfo($employee_no);
            $bio_id = $data->bsd_no;

            $monthDate = $this->dtrDate->format('m-Y');
            $logs = $this->timeLogService->getDTR($bio_id, $monthDate);

            $this->logs = [
                'employee_account' => [
                    'bsd_no' => $data->bsd_no,
                    'firstname' => $data->personal->firstname,
                    'middlename' => $data->personal->middlename,
                    'lastname' => $data->personal->lastname,
                    'position' => $data->positions->name ?? 'N/A',
                    'section' => $data->section->name ?? 'N/A',
                ],
                'dtr' => $logs
            ];

        } catch (\Exception $e) {
            $this->errors = array_merge($this->errors ?? [], explode("\n", trim($e->getMessage())));
        }
        
    }

    private function getEmployeeInfo(string $employee_no) {
        $data = EmployeeInformation::with('personal', 'section', 'positions')->where('employee_no', $employee_no)
            ->first();

        if(!is_null($data)) {
            return $data;
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
