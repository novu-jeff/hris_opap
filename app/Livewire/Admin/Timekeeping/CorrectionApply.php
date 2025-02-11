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
        $timestamp = Carbon::create($date)->format('j/n/Y');
    
        $query = EmployeeTimelogs::with('employee.personal')
            ->where('bsd_no', $bsd_no)
            ->where('logdatetime', 'LIKE', "{$timestamp}%");
    
        $records = $query->get()->unique('logdatetime'); // Remove exact duplicates
    
        $groupedData = $records->groupBy(function ($record) {
            return Carbon::parse($record->logdatetime)->format('j/n/Y') . '|' . ($record->bsd_no ?? 'undefined');
        })->map(function ($logs, $key) {
            [$date, $bsd_no] = explode('|', $key);
    
            $formattedLogs = collect($logs)->mapToGroups(function ($log) {
                return [
                    Carbon::parse($log->logdatetime)->format('H') => [
                        'time' => Carbon::parse($log->logdatetime)->format('H:i:s'),
                        'captured_image' => $log->captured_image
                    ]
                ];
            });
    
            // Only apply merging logic when log count is greater than 4
            if ($formattedLogs->flatten(1)->count() > 4) {
                $formattedLogs = $formattedLogs->map(function ($entries, $hour) {
                    if ($hour == 12 || $hour == 13) {
                        return $entries->toArray(); // Keep all values for 12 PM and 1 PM
                    } elseif ($hour < 12) {
                        return [$entries->sortBy('time')->first()]; // Keep earliest time for AM
                    } else {
                        return [$entries->sortByDesc('time')->first()]; // Keep latest time for PM
                    }
                })->collapse()->values()->all();
            } else {
                // If logs are <= 4, keep all logs as they are
                $formattedLogs = $formattedLogs->collapse()->values()->all();
            }
    
            return [
                'date' => $date,
                'bsd_no' => $bsd_no,
                'employee' => $logs->first()->employee,
                'origin' => $logs->first()->origin,
                'logs' => $formattedLogs
            ];
        })->values();
    
        return $groupedData;
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
                $model = EmployeeTimelogs::class;

                $timestamp = Carbon::create($this->date)->format('j/n/Y');

                $records = $model::where('bsd_no', $this->bsd_no)
                    ->where('logdatetime', 'LIKE', "{$timestamp}%");
                
                if ($records->delete()) {

                    // Ensure none of the times are null or empty
                    $toBeCreated = [
                        $timestamp . ' ' . ($this->clockin ?? ''),
                        $timestamp . ' ' . ($this->breakout ?? ''),
                        $timestamp . ' ' . ($this->breakin ?? ''),
                        $timestamp . ' ' . ($this->clockout ?? '')
                    ];

                    foreach ($toBeCreated as $date) {
                        if (!empty($date)) {  // Only create if the time is valid
                            EmployeeTimelogs::create([
                                'origin' => 'biometrics',
                                'bsd_no' => $this->bsd_no,
                                'isindtr' => '',
                                'logdatetime' => $date,
                                'type' => '',
                                'ismanual' => 1,
                            ]);
                        }
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
