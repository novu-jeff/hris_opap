<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeTimelogs;
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
        
        $query = EmployeeTimelogs::with('employee.personal')
            ->where('logdatetime', 'LIKE', "{$timestamp}%");
                
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
                'employee' => $logs->first()->employee,
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
        })->map(function ($entries, $hour) {
            if ($hour == 12 || $hour == 13) {
                return $entries->values()->toArray(); // Keep unique times
            } elseif ($hour < 12) {
                return [$entries->sortBy('time')->first()]; // Earliest log for AM
            } else {
                return [$entries->sortByDesc('time')->first()]; // Latest log for PM
            }
        })->collapse()->values()->all();
    }

    protected function rules() {
        return [
            'clockin' => 'required',
            'breakout' => 'required',
            'breakin' => 'required',
            'clockout' => 'required',
        ];
    }

    protected function messages()
    {
        return [
            'clockin.required' => '* required.',
            'breakout.required' => '* required.',
            'breakin.required' => '* required.',
            'clockout.required' => '* required.',
        ];
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

                $logTimes = [
                    'clockin' => ['time' => $this->clockin, 'type' => 0],   // IN
                    'breakout' => ['time' => $this->breakout, 'type' => 1], // OUT
                    'breakin' => ['time' => $this->breakin, 'type' => 0],   // IN
                    'clockout' => ['time' => $this->clockout, 'type' => 1], // OUT
                ];
            
                foreach ($logTimes as $key => $log) {
                    if (!empty($log['time'])) {
                        EmployeeTimelogs::updateOrCreate(
                            [
                                'bsd_no' => $this->bsd_no,
                                'logdatetime' => $timestamp . ' ' . $log['time'],
                            ],
                            [
                                'origin' => 'biometrics',
                                'isindtr' => '',
                                'type' => $log['type'], 
                                'ismanual' => 1,
                            ]
                        );
                    }
                }

                DB::commit();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'showAlert' => true,
                    'message' => 'Correction has been applied to clock log ID #' . $this->bsd_no
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
