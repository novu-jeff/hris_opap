<?php

namespace App\Livewire\Admin\Hris\Profile;

use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Livewire\Component;

class EmpHistoryLeaveCard extends Component
{

    public $id;
    public $employee_no;
    public $employee;
    public $action;
    public $records;

    public $period = [];
    public $particulars = [];
    public $vl_earned = [];
    public $vl_aut_w_pay = [];
    public $vl_bal = [];
    public $vl_aut_wo_pay = [];
    public $sl_earned = [];
    public $sl_aut_w_pay = [];
    public $sl_bal = [];
    public $sl_aut_wo_pay = [];
    public $remarks = [];
    public $vl_total_bal = [];
    public $sl_total_bal = [];
    public $total_bal = [];
    public $activeYear;

    public $isEditing = false;


    protected $listeners = [
        'removeYear',
    ];

    public function mount(){

       // $this->employee_no = $employee_no;

    // Load employee info
       // $this->employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();
     //   $this->employee = EmployeePersonal::where('employee_no', $this->employee_no)->first();


        $this->loadRecords();
        $this->loadEmployeeInfo();
    }

    public function loadEmployeeInfo() {

        $info = EmployeeInformation::with(['personal', 'positions', 'employment_type'])
        ->where('employee_no', $this->employee_no)
        ->first();

        if ($info) {
            $this->employee = (object)[
                'fullname' => trim($info->personal->firstname.' '.$info->personal->middlename.' '.$info->personal->lastname.' '.$info->personal->suffix),
                'employee_no' => $info->employee_no,
                'birthday' => $info->personal->birthday,
                'sex' => $info->personal->sex,
                'civil_status' => $info->personal->civil_status,
                'position' => $info->positions?->name ?? '',
                'date_hired' => $info->date_hired,
                'employment_type' => $info->employment_type?->name ?? '',
            ];
        }
    }

    public function enableEdit()
    {
        $this->isEditing = true;
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->loadRecords(); // reload from DB/service to discard unsaved changes
    }

    public function save()
    {
        $this->regroupRecords(); // re-group updated values

        // You likely have a LeaveCardService or model to persist updates.
        // Example (you can replace this with your actual update logic):
        $leaveCardService = new LeaveCardService;
        $leaveCardService->updateLeaveCard($this->employee_no, $this->records);

        $this->isEditing = false;

       /* $this->dispatchBrowserEvent('notify', [
            'message' => '✅ Leave card updated successfully!',
            'type' => 'success',
        ]);*/
    }

    public function loadRecords() {

       // $this->employee_no = Auth::user()->employee_no;

       // $employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();

       // if (!$employee) {
       //     return redirect()->route('employee.dashboard');
       // }

       //dd($this->employee_no);

        $leaveCardService = new LeaveCardService;

        $sortedRecords = $leaveCardService->getLeaveCard($this->employee_no);

        $this->records = $sortedRecords;

        $this->activeYear = array_key_last($sortedRecords->toArray());

        // Reset arrays
        $this->period = [];
        $this->particulars = [];
        $this->vl_earned = [];
        $this->vl_aut_w_pay = [];
        $this->vl_bal = [];
        $this->vl_aut_wo_pay = [];
        $this->sl_earned = [];
        $this->sl_aut_w_pay = [];
        $this->sl_bal = [];
        $this->sl_aut_wo_pay = [];
        $this->remarks = [];
        $this->vl_total_bal = [];
        $this->sl_total_bal = [];

        foreach ($sortedRecords as $year => $recordData) {
            $this->total_bal[$year] = $recordData['previous_bal'];
            foreach ($recordData['items'] as $record) {
                $this->particulars[$year][] =  $record['particulars'];
                $this->period[$year][] = $record['period'];
                $this->vl_earned[$year][] = $record['vl_earned'] ?? 0;
                $this->vl_aut_w_pay[$year][] = $record['vl_aut_w_pay'] ?? 0;
                $this->vl_bal[$year][] = $record['vl_bal'] ?? 0;
                $this->vl_aut_wo_pay[$year][] = $record['vl_aut_wo_pay'] ?? 0;
                $this->sl_earned[$year][] = $record['sl_earned'] ?? 0;
                $this->sl_aut_w_pay[$year][] = $record['sl_aut_w_pay'] ?? 0;
                $this->sl_bal[$year][] = $record['sl_bal'] ?? 0;
                $this->sl_aut_wo_pay[$year][] = $record['sl_aut_wo_pay'] ?? 0;
                $this->remarks[$year][] = $record['remarks'] ?? '';
            }
        }

    }

    public function onChange($code, $year, $key) {

        $previousKey = $key - 1;
        $previousYear = $year - 1;

        $balProperty = "{$code}_bal";
        $earnedProperty = "{$code}_earned";
        $autWPayProperty = "{$code}_aut_w_pay";

        if (isset($this->{$balProperty}[$year][$previousKey])) {
            $previousBal = floatval($this->{$balProperty}[$year][$previousKey]);
        } elseif (isset($this->{$balProperty}[$previousYear])) {
            $previousBal = floatval(end($this->{$balProperty}[$previousYear]));
        } else {
            $previousBal = 0;
        }

        while (isset($this->{$earnedProperty}[$year])) {
            foreach ($this->{$earnedProperty}[$year] as $i => $earned) {
                if ($i < $key) continue;

                $earned = floatval($earned ?? 0); 
                $autWPay = isset($this->{$autWPayProperty}[$year][$i]) ? floatval($this->{$autWPayProperty}[$year][$i]) : 0;

                $this->{$balProperty}[$year][$i] = number_format($previousBal + $earned - $autWPay, 3, '.', '');

                $previousBal = floatval($this->{$balProperty}[$year][$i]);
            }

            $this->total_bal[$year + 1][$code] = max($this->{$balProperty}[$year]);

            $year++;
            $key = 0; 
        }
    }

    public function regroupRecords() {

        $updatedRecords = [];

        foreach ($this->period as $year => $periods) {
            $items = [];

            foreach ($periods as $index => $period) {
                $items[] = [
                    'period' => $period,
                    'particulars' => $this->particulars[$year][$index] ?? '',
                    'vl_earned' => (float)($this->vl_earned[$year][$index] ?? 0),
                    'vl_aut_w_pay' => (float)($this->vl_aut_w_pay[$year][$index] ?? 0),
                    'vl_bal' => (float)($this->vl_bal[$year][$index] ?? 0),
                    'vl_aut_wo_pay' => (float)($this->vl_aut_wo_pay[$year][$index] ?? 0),
                    'sl_earned' => (float)($this->sl_earned[$year][$index] ?? 0),
                    'sl_aut_w_pay' => (float)($this->sl_aut_w_pay[$year][$index] ?? 0),
                    'sl_bal' => (float)($this->sl_bal[$year][$index] ?? 0),
                    'sl_aut_wo_pay' => (float)($this->sl_aut_wo_pay[$year][$index] ?? 0),
                    'remarks' => $this->remarks[$year][$index] ?? '',
                ];
            }

            $items = collect($items)->sortBy(function ($item) {
                return DateTime::createFromFormat('F', $item['period'])->format('m');
            })->values();

            $prevBal = [
                'vl' => (float)($this->total_bal[$year]['vl'] ?? 0),
                'sl' => (float)($this->total_bal[$year]['sl'] ?? 0),
            ];

            $updatedRecords[$year] = [
                'previous_bal' => $prevBal,
                'items' => $items,
            ];
        }

        $this->records = $updatedRecords;
    }

    public function setActiveYear(string $year) {
        $this->activeYear = $year;
    }

    public function render()
    {
        
        return view('livewire.admin.hris.profile.emp-history-leave-card');
    }

}
