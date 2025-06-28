<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Http\Controllers\Admin\Services\TimeLogService;
use App\Models\EmployeeAUT;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\EmployeeTimelogs;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CorrectionApply extends Component
{

    public $bsd_no;
    public $date;
    public $clockin;
    public $breakout;
    public $breakin;
    public $clockout;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        if (is_null($this->bsd_no) && is_null($this->date)) {
            return redirect()->route('timekeeping.correction');
        }
    
        $logs = $this->getLogs($this->bsd_no, $this->date);

        if (!empty($logs)) {

            $formatTime = fn($value) => !empty($value) ? Carbon::parse($value)->format('H:i') : null;
            $this->clockin = $formatTime($logs['clock_in'] ?? null);
            $this->breakout = $formatTime($logs['lunch_in'] ?? null);
            $this->breakin = $formatTime($logs['lunch_out'] ?? null);
            $this->clockout = $formatTime($logs['clock_out'] ?? null);

        } else {
            $this->clockin = $this->breakout = $this->breakin = $this->clockout = null;
        }
        
    }

    private function getLogs(? int $bsd_no = null, string $date) {

        $timestamp = Carbon::create($date)->format('Y-m-d');
        
        $logService = new TimeLogService;
                
        $logs = $logService->getLogs($timestamp);

        $logs = $logs[$timestamp][$bsd_no] ?? null;

        return $logs ? $logs : [];
    }
    
    protected function rules()
    {
        return [
            'clockin'  => 'required|date_format:H:i',
            'breakout' => 'required|date_format:H:i|after:clockin',
            'breakin' => 'required|date_format:H:i|after_or_equal:breakout',
            'clockout' => 'required|date_format:H:i|after:breakin',
        ];
    }

    protected function messages()
    {
        return [
            'clockin.required'  => '* required.',
            'breakout.required' => '* required.',
            'breakin.required'  => '* required.',
            'clockout.required' => '* required.',
    
            'breakout.after' => '* must be later than clock-in time.',
            'breakin.after_or_equal'  => '* must be later than or same break-out time.',
            'clockout.after' => '* must be later than break-in time.',
    
            'clockin.date_format'  => '* must be a valid time format (HH:mm).',
            'breakout.date_format' => '* must be a valid time format (HH:mm).',
            'breakin.date_format'  => '* must be a valid time format (HH:mm).',
            'clockout.date_format' => '* must be a valid time format (HH:mm).',
        ];
    }
    

    private function employeeShift() {
        
        $shift = EmployeeInformation::select('shift_id')->where('bsd_no', $this->bsd_no)->first();
    
        if (is_null($shift) || is_null($shift->shift_id)) {
            return null;
        }
    
        $record = ShiftSchedule::find($shift->shift_id);
    
        if (is_null($record)) {
            return null;
        }
    
        return $record;
    }

    private function computeAut($logs) {
        
        $shift = $this->employeeShift();

        if(is_null($shift)) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Unable to apply corrections due to missing shift schedule of this employee.', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);

            return;
        }

        if($shift->shift_duration == 'flexible') {
            $earliestIn = Carbon::parse($shift->earliest_in);
            $latestIn = Carbon::parse($shift->latest_in);
        } else {
            $earliestIn = Carbon::parse($shift->start_shift);
            $earliestIn = Carbon::parse($shift->end_shift);
        }

        $aut = [];

        $breakTimeStart = Carbon::parse($shift->break_out);
        $breakTimeEnd = Carbon::parse($shift->break_in);

        
        $firstLog = Carbon::parse($logs[0]);

        if ($firstLog->greaterThan($latestIn)) {
                
            $lateMins = $firstLog->diffInMinutes($latestIn);
    
            $aut = [
                'late' => $lateMins,
            ];
        }

        $firstLog = Carbon::parse($logs[0]);
        $secondLog = Carbon::parse($logs[1]);

        if($firstLog->lessThan($earliestIn)) {
            $expectedOut = $earliestIn->copy()->addHours(9);
        } else if($firstLog->between($earliestIn, $latestIn)) {
            $expectedOut = $firstLog->copy()->addHours(9);
        } else {
            $expectedOut = Carbon::parse('18:00');
        }

        $outLog = !empty($logs) && count($logs) > 1 ? Carbon::parse(end($logs)) : $expectedOut;

        if ($outLog->lessThan($expectedOut) || $outLog->equalTo($expectedOut)) {
            $undertimeMinutes = $expectedOut->diffInMinutes($outLog);
            $aut['undertime'] = ($aut['undertime'] ?? 0) + $undertimeMinutes;
        }
        
        if (!$secondLog->between($breakTimeStart, $breakTimeEnd)) {
            $undertimeMinutes = $breakTimeStart->diffInMinutes($secondLog);
            $aut['undertime'] = ($aut['undertime'] ?? 0) + $undertimeMinutes;
        }
        

        return $aut;

    }

    public function save(bool $isNotify = true)
    {
        if (Gate::denies('write correction-timelogs')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->validate();

        if ($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'The action cannot be undone or reverted!',
                'action' => 'save',
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $dateOnly = Carbon::create($this->date)->format('Y-m-d');
            $external = config('app.external_timelogs');

            $logs = [
                $this->clockin,
                $this->breakout,
                $this->breakin,
                $this->clockout
            ];

            $aut = $this->computeAut($logs);

            $logTimes = [
                'clockin'  => ['time' => $this->clockin, 'type' => 0],
                'breakout' => ['time' => $this->breakout, 'type' => 1],
                'breakin'  => ['time' => $this->breakin, 'type' => 0],
                'clockout' => ['time' => $this->clockout, 'type' => 1],
            ];

            // Delete old records for the date
            EmployeeTimelogs::where('employee_id', $this->bsd_no)
                ->where('timestamp', 'LIKE', "{$dateOnly}%")
                ->delete();

            // Re-insert updated time logs
            foreach ($logTimes as $log) {
                if (!empty($log['time'])) {
                    $timestampFull = "{$this->date} {$log['time']}:00";

                    $data = [
                        'employee_id' => $this->bsd_no,
                        'timestamp' => $timestampFull,
                        'status1' => $log['type'],
                    ];

                    if ($external) {
                        $data = array_merge($data, [
                            'sn' => 'RUU5242500021',
                            'table' => 'ATTLOG',
                            'stamp' => '9999',
                        ]);
                    } else {
                        $data['status'] = $log['type'];
                    }

                    EmployeeTimelogs::create($data);
                }
            }

            // Save AUT record
            EmployeeAUT::updateOrCreate(
                [
                    'date' => $dateOnly,
                    'bsd_no' => $this->bsd_no,
                ],
                [
                    'lates' => $aut['late'] ?? 0,
                    'undertime' => $aut['undertime'] ?? 0,
                    'absences' => $aut['absences'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => 'Correction has been applied to BSD # ' . $this->bsd_no,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }


    public function render()
    {
        return view('livewire.admin.timekeeping.correction-apply');
    }
}
