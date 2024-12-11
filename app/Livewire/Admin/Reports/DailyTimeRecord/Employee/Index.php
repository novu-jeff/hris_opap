<?php

namespace App\Livewire\Admin\Reports\DailyTimeRecord\Employee;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
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

    protected $listeners = ['updateDate'];

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
        Log::info('Before Change Month: ' . $this->dtrDate);
    
        // Increment the current date by the specified number of months
        $currentDate = Carbon::createFromFormat('F, Y', $this->dtrDate);
        $currentDate->addMonths($increment); // Increase or decrease the month
    
        // Update dtrDate with the new incremented date in 'F, Y' format
        $this->dtrDate = $currentDate->format('F, Y');
    
        // Update employee_id
        $this->employee_id = $employee_id;
    
        // Load the DTR for this employee
        $this->changeDTR($this->employee_id);
    }
    
    public function showDtr($id) {
        
        $this->employee_id = $id;
        $this->dtrDate = $this->date ?? now()->format('F, Y'); // change date to selected date or current

        $this->changeDTR($id);

        // Dispatch the modal
        $this->dispatch('showModal', [
            'modal' => 'dtrModal'
        ]);
    }

    public function changeDTR($id)
    {
        $date = Carbon::createFromFormat('F, Y', $this->dtrDate);

        Log::info('Showing DTR for Date: ' . $this->dtrDate);

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $dtr = DB::table('employee_account')
        ->leftJoin('employee_clock_in_out', function ($join) use ($startDate, $endDate) {
            $join->on('employee_account.employee_no', '=', 'employee_clock_in_out.employee_no')
                ->whereBetween('employee_clock_in_out.created_at', [$startDate, $endDate])
                ->orWhereNull('employee_clock_in_out.created_at');
        })
        ->leftJoin('employee_personal', 'employee_account.employee_no', '=', 'employee_personal.employee_no')
        ->leftJoin('employee_information', 'employee_account.employee_no', '=', 'employee_information.employee_no')
        ->leftJoin('positions', 'employee_information.position_id', '=', 'positions.id')
        ->leftJoin('sections', 'employee_information.section_id', '=', 'sections.id')
        ->leftJoin('departments', 'sections.department_id', '=', 'departments.id')
        ->select(
            'employee_account.employee_no',
            'employee_personal.firstname',
            'employee_personal.middlename',
            'employee_personal.lastname',
            'employee_clock_in_out.clock_in_am',
            'employee_clock_in_out.clock_out_am',
            'employee_clock_in_out.clock_in_pm',
            'employee_clock_in_out.clock_out_pm',
            'employee_clock_in_out.total_mins_consumed',
            
            'employee_clock_in_out.created_at',
            'positions.code as position_code',
            'positions.name as position_name',
            'positions.salary',
            'departments.name as department_name',
            'departments.code as department_code'
        )
        ->where('employee_account.employee_no', $id)
        ->get();

        $allDays = collect();
        foreach ($startDate->toPeriod($endDate) as $day) {
            $allDays->push($day->toDateString()); 
        }

        $mappedClockData = [];
        foreach ($allDays as $day) {

            $clockData = $dtr->firstWhere(function($item) use ($day) {
                return Carbon::parse($item->created_at)->isSameDay(Carbon::parse($day));
            });

            $mappedClockData[] = [
                'date' => $day,
                'clock_in_am' => $clockData ? $clockData->clock_in_am : null,
                'clock_out_am' => $clockData ? $clockData->clock_out_am : null,
                'clock_in_pm' => $clockData ? $clockData->clock_in_pm : null,
                'clock_out_pm' => $clockData ? $clockData->clock_out_pm : null,
                'total_mins_consumed' => $clockData ? $clockData->total_mins_consumed : null,
            ];
        }

        // Assign data to $this->dtr
        $this->dtr = [
            'employee_account' => [
                'employee_no' => $dtr->first()->employee_no,
                'firstname' => $dtr->first()->firstname,
                'lastname' => $dtr->first()->lastname,
                'middlename' => $dtr->first()->middlename ? strtoupper(substr($dtr->first()->middlename, 0, 1)) . '.' : '',
                'position' => $dtr->first()->position_name . ' (' . $dtr->first()->position_code . ')',
                'department' => $dtr->first()->department_name . ' (' . $dtr->first()->department_code . ')',
                'salary' => $dtr->first()->salary,
            ],
            'clock_in_out' => $mappedClockData
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports.daily-time-record.employee.index');
    }
}
