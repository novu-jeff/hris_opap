<?php

namespace App\Livewire\Admin\Timekeeping;

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
            
        if (!empty($logs) && isset($logs[0])) {
            $records = $logs[0]['logs'] ?? [];
            $this->clockin = isset($records[0]['time']) ? Carbon::parse($records[0]['time'])->format('H:i') : null;
            $this->breakout = isset($records[1]['time']) ? Carbon::parse($records[1]['time'])->format('H:i') : null;
            $this->breakin = isset($records[2]['time']) ? Carbon::parse($records[2]['time'])->format('H:i') : null;
            $this->clockout = isset($records[3]['time']) ? Carbon::parse($records[3]['time'])->format('H:i') : null;  // Corrected this line

        } else {
            $this->clockin = $this->breakout = $this->breakin = $this->clockout = null;
        }
        
    }

    private function getLogs(int $bsd_no = null, string $date) {

        $timestamp = Carbon::create($date)->format('d/m/Y');
        
        $query = EmployeeTimelogs::where('logdatetime', 'LIKE', "{$timestamp}%");
                
        if (!is_null($bsd_no)) {
            $query->where('bsd_no', $bsd_no);
        }
    
        $records = $query->get();
    
        return $records->groupBy(function ($record) {
            return Carbon::createFromFormat('d/m/Y H:i', $record->logdatetime)->format('d/m/Y') . '|' . ($record->bsd_no ?? 'undefined');
        })->map(function ($logs, $key) {
            [$date, $bsd_no] = explode('|', $key);
    
            return [
                'date' => $date,
                'bsd_no' => $bsd_no,
                'origin' => $logs->first()->origin,
                'logs' => $this->processLogs($logs)
            ];
        })->values();
    }
    
    /**
     * Process logs to merge IN/OUT timestamps.
     */
    private function processLogs($logs)
    {
        return $logs->mapToGroups(function ($log) {
            $logTime = Carbon::createFromFormat('d/m/Y H:i', $log->logdatetime);
            return [
                $logTime->format('H') => [
                    'time' => $logTime->format('H:i'),
                    'captured_location' => $log->captured_location,
                    'captured_image' => $log->captured_image,
                    'accomplishment' => $log->accomplishment
                ]
            ];
        })->collapse()->values()->all();
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
    
        // Check if shift_id is null
        if (is_null($shift) || is_null($shift->shift_id)) {
            return null;
        }
    
        // Find the shift schedule record
        $record = ShiftSchedule::find($shift->shift_id);
    
        // Check if the record is null
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

        // for late
        if ($firstLog->greaterThan($latestIn)) {
                
            $lateMins = $firstLog->diffInMinutes($latestIn);
    
            $aut = [
                'late' => $lateMins,
            ];
        }

        // for undertime
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
            $aut['undertime'] = ($aut['undertime'] ?? 0) + $undertimeMinutes; // Accumulate undertime
        }
        
        if (!$secondLog->between($breakTimeStart, $breakTimeEnd)) {
            $undertimeMinutes = $breakTimeStart->diffInMinutes($secondLog);
            $aut['undertime'] = ($aut['undertime'] ?? 0) + $undertimeMinutes; // Accumulate undertime
        }
        

        return $aut;

    }

    public function save(bool $isNotify = true) {

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
                'action' => 'save'
            ]);
        } else {

            DB::beginTransaction();

            try {

                $timestamp = Carbon::create($this->date)->format('d/m/Y');

                $logs = [
                    $this->clockin,
                    $this->breakout,
                    $this->breakin,
                    $this->clockout
                ];

                $aut = $this->computeAut($logs);

                $logTimes = [
                    'clockin'  => ['time' => $this->clockin, 'type' => 0], // IN
                    'breakout' => ['time' => $this->breakout, 'type' => 1], // OUT
                    'breakin'  => ['time' => $this->breakin, 'type' => 0], // IN
                    'clockout' => ['time' => $this->clockout, 'type' => 1], // OUT
                ];
                
                // Delete existing records for the same timestamp
                EmployeeTimelogs::where('bsd_no', $this->bsd_no)
                    ->where('logdatetime', 'LIKE', "{$timestamp}%")
                    ->delete();
                
                // Re-insert the new records
                foreach ($logTimes as $key => $log) {
                    if (!empty($log['time'])) {
                        EmployeeTimelogs::create([
                            'bsd_no'      => $this->bsd_no,
                            'logdatetime' => $timestamp . ' ' . $log['time'],
                            'origin'      => 'biometrics',
                            'isindtr'     => '',
                            'type'        => $log['type'], 
                            'ismanual'    => 1,
                        ]);
                    }
                }
                

                EmployeeAUT::updateOrCreate(
                    [
                        'date' => $timestamp,
                        'bsd_no' => $this->bsd_no,
                    ],
                    [
                        'lates' => $aut['late'] ?? 0,
                        'undertime' => $aut['undertime'] ?? 0,
                        'absences' => $aut['absences'] ?? 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                DB::commit();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'showAlert' => true,
                    'message' => 'Correction has been applied to BSD # ' . $this->bsd_no
                ]);

            } catch (\Exception $e) {

                DB::rollBack();

                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }


    public function render()
    {
        return view('livewire.admin.timekeeping.correction-apply');
    }
}
