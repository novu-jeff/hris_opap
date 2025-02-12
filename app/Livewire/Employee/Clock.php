<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\EmployeeTimelogs;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Clock extends Component
{

    public $user_id;
    public $bsd_no;
    public $isClockedIn = false;
    public $isClockedOut = false;
    public $hasClearImage = false;
    public $capturedImage = 1;
    public $logs;
    public $accomplishment;
    public $isForcedClockout = false;
    public $manipulate_timestamp = '07:00';

    public $status;

    protected $listeners = ['triggerClock', 'triggerClockOut', 'grabImage', 'saveAccomplishment'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_no;
        $bsd_no = EmployeeInformation::where('employee_no', $user_id)
            ->first()
            ->bsd_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.clock');
        };

        $this->user_id = $user_id;
        $this->bsd_no = $bsd_no;

        $this->toggleStatus();

    }


    public function delete() {
        $date = Carbon::now()->format('j/n/Y');
        EmployeeTimelogs::where('logdatetime', 'like', "%{$date}%")->delete();
    }


    public function employeeShift() {
        $shift = EmployeeInformation::select('shift_id')->where('employee_no', $this->user_id)->first();
    
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
    
    public function triggerClock(bool $isNotify = true) {

        $shift = $this->employeeShift();

        $date = Carbon::now()->format('j/n/Y');

        $model = EmployeeTimelogs::where('bsd_no', $this->bsd_no)
            ->where('logdatetime', 'LIKE', "{$date}%");
        $clockRecords = $model->get();
        
        $entry = $model->count();

        $hasAccomplishment = $clockRecords->contains(function ($record) {
            return !empty($record['accomplishment']);
        });

        if(is_null($shift)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Please be informed!',
                'message' => 'Your clock-in or clock-out actions cannot be processed as no shift schedule is currently assigned to you. Kindly contact HR for further assistance.',
            ]);
        }

        if($shift->work_setup !== 'hybrid') {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Please be informed!',
                'message' => 'Your clock-in or clock-out actions cannot be processed as the work setup in your shift is only for onsite.',
            ]);
        }

        if($hasAccomplishment) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Please be informed!',
                'message' => 'You have completed your work hours today. No actions available for clock-in or clock-out.',
            ]);
        }

        if($entry < 4) {

            // $time = Carbon::now();
            $time = $this->manipulate_timestamp;

            $entry = $entry + 1;

            if($isNotify) {
                $this->processLog($entry, $time);
            } else {
                $this->dispatch('captureImage', ['time' => $time]);
                $this->insertLog($entry, $time);
            }

        } else {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'warning',
                'title' => 'Please be informed!',
                'message' => 'You have completed your work hours today. No actions available for clock-in or clock-out.',
            ]);
        }

    }

    public function triggerClockOut(bool $isNotify = true) {
        
        $time = Carbon::now();
        $time = Carbon::parse($time);

        if($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Please be informed!,',
                'plugin' => [
                    'textarea',
                    'title' => 'Please write your today\'s accomplishment report.',
                ],
                'time' => $time,
                'message' => 'You\'re clocking-out earlier than your expected time which may be considered and marked as undertime.',
                'action' => 'saveAccomplishment',
            ]);  
            
            return false;

        } else {

            $this->dispatch('captureImage', ['time' => $time]);
            $this->insertLog(4, $time);

        }

    }

    # handle processing
    public function processLog($entry, $time) {

        $time = Carbon::parse($time);
        $date = Carbon::now()->format('F d, Y');
        $timeFormatted = $time->format('h:i a');
    
        $logTypes = [
            1 => 'Clock-In',
            2 => 'Break-Out',
            3 => 'Break-In',
            4 => 'Clock-Out'
         ];
                     
        if (isset($logTypes[$entry]) && $this->checkLog($entry, $time)) {
            $this->dispatch('showConfirmation', [
                'title' => "Confirm {$logTypes[$entry]}",
                'message' => '
                    <div>
                        <p class="mt-2 mb-2 text-uppercase fw-bold">'.strtoupper($date).'</p>
                        <h1 class="text-uppercase fw-bold">'.strtoupper($timeFormatted).'</h1>
                    </div>
                ',
                'action' => 'triggerClock',
            ]);
        }
    }

    # handle minor validation
    public function checkLog($entry, $timestamp) {

        $shift = $this->employeeShift();

        if($shift->shift_duration == 'flexible') {

            $earliestClockIn = Carbon::parse($shift->web_earliest_clockin);
            $latestClockIn = Carbon::parse($shift->web_latest_clockin);

            $breakTimeFrom = Carbon::parse($shift->break_out);
            $breakTimeTo = Carbon::parse($shift->break_in);

            $date = Carbon::now()->format('j/n/Y');
            $time = Carbon::now()->format('H:i');
            
            $clockRecords = EmployeeTimelogs::where('bsd_no', $this->bsd_no)
                ->where('logdatetime', 'LIKE', "{$date}%");
            
            $firstLog = $clockRecords->first()->logdatetime ?? null;
            $expectedOut = null;
            
            if ($firstLog) {
                if ($entry > 1) {
                    // Convert firstLog to Carbon instance
                    $firstLogTime = Carbon::createFromFormat('d/m/Y H:i', $firstLog);  
            
                    // Default expectedOut to firstLog + 9 hours
                    $expectedOut = $firstLogTime->copy()->addHours(9);
            
                    // Check if firstLogTime is within clock-in range
                    if (!$firstLogTime->between($earliestClockIn, $latestClockIn, true)) {
                        $expectedOut = Carbon::createFromTime(17, 0, 0); // Default to 5:00 PM
                    }
                }
            }
            
            // Ensure expectedOut is properly formatted even if it's null
            $formattedExpectedClockOut = $expectedOut ? $expectedOut->format('h:i A') : 'N/A';
            $breakTimeFromFormatted = Carbon::parse($breakTimeFrom)->format('h:i A');
            $breakTimeToFormatted = Carbon::parse($breakTimeTo)->format('h:i A');

            if($entry == 1) {

                # flexible
                
                // if first log
                // check if log is after allowed clockin
                if($timestamp->lt($earliestClockIn)) {
                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'info',
                        'title' => 'Please be informed!',
                        'message' => 'Unable to clock in because the earliest allowed clock-in is <strong>' . $earliestClockIn->format('g:i A') . '</strong>.',
                    ]);
    
                    return false;
                }

                if ($timestamp->gt($latestClockIn)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Are you sure to continue?',
                        'message' => 'You\'re clocking-in later than your expected time of <strong>' . $latestClockIn->format('g:i A') . '</strong>. This may be considered and marked as late.',
                        'action' => 'triggerClock',
                    ]);

                    return false;
                }
        
            }

            if($entry == 2) {

                if($firstLog && $timestamp->lt($breakTimeFrom)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Are you sure to continue?',
                        'message' => 'You\'re attempting to break-out earlier than your expected time of <strong>' . $breakTimeFromFormatted . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'triggerClock',
                    ]);

                    return false;
                }

                if($firstLog && $timestamp->lt($expectedOut) && !$timestamp->between($breakTimeFrom, $breakTimeTo) ) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Please be informed!,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.',
                        ],
                        'time' => $time,
                        'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'saveAccomplishment',
                    ]);  
                    
                    return false;
                }

                if($firstLog && $timestamp->gt($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Before clocking out,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.'
                        ],
                        'time' => $time,
                        'message' => '',
                        'action' => 'saveAccomplishment',
                    ]);  

                    return false;
                }

                if($timestamp->gt($breakTimeTo) && $timestamp->lt($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Please be informed!,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.',
                        ],
                        'time' => $time,
                        'isForcedClockout' => false,
                        'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'saveAccomplishment',
                    ]);  
                    
                    return false;
                }

                if($timestamp->gte($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Before clocking out,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.'
                        ],
                        'time' => $time,
                        'message' => '',
                        'action' => 'saveAccomplishment',
                    ]);  

                    return false;
                }
            }

            if($entry == 3) {

                if($firstLog && $timestamp->lt($breakTimeFrom)) {
                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'info',
                        'title' => 'Please Be Informed',
                        'message' => 'We\'ve noticed that your break-out was earlier than expected time. You\'re expected break-in is from <strong>' . $breakTimeFromFormatted . ' - ' . $breakTimeToFormatted . '</strong>',
                    ]);

                    return false;
                }

                if($firstLog && $timestamp->gt($breakTimeTo) && $timestamp->lt($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Please be informed!,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.',
                        ],
                        'time' => $time,
                        'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'saveAccomplishment',
                    ]);  
                    
                    return false;
                }

                if($firstLog && $timestamp->gt($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Before clocking out,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.'
                        ],
                        'time' => $time,
                        'message' => '',
                        'action' => 'triggerClock',
                    ]);  

                    return false;
                }

                if($timestamp->gt($breakTimeTo) && $timestamp->lt($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Please be informed!,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.',
                        ],
                        'time' => $time,
                        'isForcedClockout' => false,
                        'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'saveAccomplishment',
                    ]);  
                    
                    return false;
                }

                if($timestamp->gte($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Before clocking out,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.'
                        ],
                        'time' => $time,
                        'message' => '',
                        'action' => 'saveAccomplishment',
                    ]);  

                    return false;
                }

            }

            if($entry == 4) {
                
                if($timestamp->gt($breakTimeTo) && $timestamp->lt($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Please be informed!,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.',
                        ],
                        'time' => $time,
                        'isForcedClockout' => false,
                        'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'saveAccomplishment',
                    ]);  
                    
                    return false;
                }

                if($timestamp->gte($expectedOut)) {
                    $this->dispatch('showConfirmation', [
                        'title' => 'Before clocking out,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.'
                        ],
                        'time' => $time,
                        'message' => '',
                        'action' => 'saveAccomplishment',
                    ]);  

                    return false;
                }

            }

        }
        
        return true;

    }
   
    # hanlde inserting log to db
    public function insertLog($entry, $time) {

        $date = Carbon::now()->format('j/n/Y');
        $time = Carbon::parse($time)->format('H:i');

        $timestamp = $date . ' ' . $time;

        $location = $this->getLocation();

        EmployeeTimelogs::create([
            'origin' => 'web',
            'bsd_no' => $this->bsd_no,
            'logdatetime' => $timestamp,
            'captured_location' => $location,
            'accomplishment' => $this->accomplishment ?? null,
        ]);   

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Recorded!', 
            'showAlert' => true,
        ]);

        $this->toggleStatus();

        return;

    }

    # handle the capturing of image
    public function grabImage($image, $time,  $hasClearImage) {
        
        $this->hasClearImage = $hasClearImage;

        if (!$hasClearImage) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Oops',
                'message' => 'Unable to clock in or clock out due to the absence of a captured image. Please ensure that your browser\'s camera is enabled and positioned to capture your face clearly.'
            ]);
        }

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $this->user_id . '_' . time() . '.png';

        $date = Carbon::now()->format('j/n/Y');
        $time = Carbon::parse($time)->format('H:i');

        $timestamp = $date . ' ' . $time;

        # store image
        Storage::disk('public')->put('timelogs/' . $imageName, base64_decode($image));

        # store image name

        EmployeeTimelogs::where('bsd_no', $this->bsd_no)
            ->where('logdatetime', $timestamp)
            ->update([
                'captured_image' => $imageName,
            ]);

    }

    # save if accomplishment needed
    public function saveAccomplishment($data) {
        $this->accomplishment = $data['report'];
        $this->triggerClock(false);

    }
    
    private function getLogs() {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
    
        $records = EmployeeTimelogs::with('employee.personal')
            ->where('bsd_no', $this->bsd_no)
            ->whereRaw("MONTH(STR_TO_DATE(logdatetime, '%d/%m/%Y %H:%i')) = ?", [$month])
            ->whereRaw("YEAR(STR_TO_DATE(logdatetime, '%d/%m/%Y %H:%i')) = ?", [$year])
            ->get();
    
        $groupedData = $records->groupBy(function ($record) {
            return Carbon::parse($record->logdatetime)->format('j/n/Y') . '|' . ($record->bsd_no ?? 'undefined');
        })->map(function ($logs, $key) {
            [$date, $bsd_no] = explode('|', $key);
            
            // Extract logs and sort by time
            $logEntries = $logs->sortBy('logdatetime')->values();
            
            // If exactly two records exist and the last one has an "accomplishment"
            if ($logEntries->count() === 2 && !empty($logEntries->last()->accomplishment)) {
                return [
                    'date' => $date,
                    'bsd_no' => $bsd_no,
                    'employee' => $logs->first()->employee,
                    'origin' => $logs->first()->origin,
                    'logs' => [
                        [
                            'time' => Carbon::parse($logEntries[0]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[0]->captured_image,
                            'captured_location' => $logEntries[0]->captured_location
                        ],
                        [], // Second empty array
                        [], // Third empty array
                        [
                            'time' => Carbon::parse($logEntries[1]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[1]->captured_image,
                            'captured_location' => $logEntries[1]->captured_location,
                            'accomplishment' => $logEntries[1]->accomplishment
                        ]
                    ]
                ];
            }

            if ($logEntries->count() === 3 && !empty($logEntries->last()->accomplishment)) {
                return [
                    'date' => $date,
                    'bsd_no' => $bsd_no,
                    'employee' => $logs->first()->employee,
                    'origin' => $logs->first()->origin,
                    'logs' => [
                        [
                            'time' => Carbon::parse($logEntries[0]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[0]->captured_image,
                            'captured_location' => $logEntries[0]->captured_location
                        ],
                        [
                            'time' => Carbon::parse($logEntries[1]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[1]->captured_image,
                            'captured_location' => $logEntries[1]->captured_location,
                            'accomplishment' => $logEntries[1]->accomplishment
                        ],
                        [], // Third empty array
                        [
                            'time' => Carbon::parse($logEntries[2]->logdatetime)->format('H:i:s'),
                            'captured_image' => $logEntries[2]->captured_image,
                            'captured_location' => $logEntries[2]->captured_location,
                            'accomplishment' => $logEntries[2]->accomplishment
                        ]
                    ]
                ];
            }
    
            return [
                'date' => $date,
                'bsd_no' => $bsd_no,
                'employee' => $logs->first()->employee,
                'origin' => $logs->first()->origin,
                'logs' => collect($logs)->map(function ($log) {
                    return [
                        'time' => Carbon::parse($log->logdatetime)->format('H:i:s'),
                        'captured_image' => $log->captured_image,
                        'captured_location' => $log->captured_location
                    ];
                })->values()->all()
            ];
        })->values();
    
        return $groupedData;
    }
    

    public function showLogs() {

        $records = $this->getLogs();

        // dd($records->toArray());

        $this->logs = $records;

        $this->dispatch('showModal', [
            'modal' => 'logs_modal'
        ]);

    }

    public function getLocation() {

        $ip = request()->ip();
        $api = env('IPINFO_API');
        $response = Http::get("http://ipinfo.io/{$ip}/json?token={$api}");

        if ($response->successful()) {
            $locationData = $response->json();

            if (!$locationData) {
                return 'running in local environment';
            }

            if(!isset($locationData['loc'])) {
                return 'running in local environment';
            }

            $location = explode(',', $locationData['loc']);
            $latitude = $location[0]; 
            $longitude = $location[1];

            return $latitude . ' ' . $longitude;
        }
    }

    public function toggleStatus() {

        $date = Carbon::now()->format('j/n/Y');

        $model = EmployeeTimelogs::where('bsd_no', $this->bsd_no)
            ->where('logdatetime', 'LIKE', "{$date}%");
    
        $clockRecords = $model->get();
        
        $entry = $model->count();

        $hasAccomplishment = $clockRecords->contains(function ($record) {
            return !empty($record['accomplishment']);
        });

        $entry = $model->count();

        $shift = $this->employeeShift();

        if($shift && $shift->shift_duration == 'flexible') {

    
            $date = Carbon::now()->format('j/n/Y');
                        
            if($entry == 0) {

                $this->status = 'Clock In';
               
            }

            if($entry == 1) {

                $this->status = 'Break Out';
              
            }

            if($entry == 2) {

                $this->status = 'Break In';

            }

            if($entry == 3) {
                
                $this->status = 'Clock Out';

            }

            if($entry == 4 || $hasAccomplishment) {
                $this->status = 'Done';
            }

        } else {
            $this->status = 'Clock In';
        }
        

        return true;

    }

    public function render()
    {
        return view('livewire.employee.clock');
    }
}