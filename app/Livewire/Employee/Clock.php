<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
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
    public $isImageCaptured = false;
    public $capturedImage;
    public $capturedLocation;
    public $logs;
    public $accomplishment;
    public $isForcedClockout = false;
    public $manipulate_timestamp = '07:00';

    public $status;

    protected $listeners = ['triggerClock', 'triggerClockOut'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.clock');
        };

        $this->user_id = $user_id;

        $information = EmployeeInformation::where('employee_no', $user_id)->first();

        $this->bsd_no = $information->bsd_no ?? null;

        $timestamp = Carbon::today();

        $records = EmployeeClockInOut::where('employee_no', $user_id)
            ->whereDate('created_at', $timestamp)->first();

        $this->toggleStatus($records);
    }


    public function delete() {
        $timestamp = Carbon::today();
        EmployeeClockInOut::whereDate('created_at', $timestamp)->delete();
    }

    public function toggleStatus($records)
    {

        $shift = $this->employeeShift();

        if(is_null($shift)) {
            return $this->status = 'Clock In';
        }

        $breakTimeFrom = Carbon::parse($shift->break_out);
        $breakTimeTo = Carbon::parse($shift->break_in);

        // $timestamp = Carbon::now();
        $timestamp = Carbon::parse($this->manipulate_timestamp);

        if(!is_null($records)) {
            if (!is_null($records->clock_in_am) && is_null($records->clock_out_am) && $timestamp->lte($breakTimeFrom)) {
                $this->status = 'Break Out';
            } 

            if (!is_null($records->clock_in_am) && is_null($records->clock_out_am) && $timestamp->gt($breakTimeFrom)) {
                $this->status = 'Clock Out';
            } 
            
            if (is_null($records->clock_in_am) && is_null($records->clock_out_am) && is_null($records->clock_in_pm)) {
                $this->status = 'Clock In';
            } 
            
            if (!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && is_null($records->clock_in_pm)) {
                $this->status = 'Break In';
            } 
            
            if (!is_null($records->clock_in_am) && !is_null($records->clock_out_am) 
                && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                $this->status = 'Clock Out';
            } 
            
            if (is_null($records->clock_in_am) && is_null($records->clock_out_am) 
                && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                $this->status = 'Clock Out';
            }
            
            if (!is_null($records->clock_out_pm)) {
                $this->status = 'Done';
            }
        } else {
            $this->status = 'Clock In';
        }

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
    
    public function triggerClock(bool $isNotify = true, $data = null)
    {

        $shift = $this->employeeShift();

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
        
        $breakTimeFrom = Carbon::parse($shift->break_out);
        $breakTimeFromFormatted = $breakTimeFrom->format('g:i A');
        
        $breakTimeTo = Carbon::parse($shift->break_in);
        $breakTimeToFormatted = $breakTimeTo->format('g:i A');

        $earliestClockIn = Carbon::parse($shift->web_earliest_clockin);
        $latestClockIn = Carbon::parse($shift->web_latest_clockin);

        // Current time (timestamp)
        // $timestamp = Carbon::now();
        $timestamp = Carbon::parse($this->manipulate_timestamp);
        $timestampFormatted = $timestamp->format('g:i A');

        $amOrPm = strtolower($timestamp->format('A')); // AM or PM
        
        // Get the clock-in/out records for the current employee
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', $timestamp)
            ->first();


        if($shift->shift_duration == 'flexible') {
            if ($records && $records->clock_in_am !== $records->clock_out_am && $records->clock_in_am !== $records->clock_in_pm) {
                $clockInTime = $records->clock_in_am;
                $maxClockOut = Carbon::parse($clockInTime)->addHours(9);
            } else {
                $maxClockOut = Carbon::createFromTime(17, 0, 0);
            }
        } else {
            $maxClockOut = Carbon::parse($shift->end_shift);
        }

        // If notifications are enabled, show alerts accordingly
        if ($isNotify) {

            // FOR CLOCK IN AM SHIFT
            if(is_null($records)) {

                # Early clock in error for am shift
                if ($timestamp->lt($earliestClockIn)) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'info',
                        'title' => 'Please be informed!',
                        'message' => 'Unable to clock in because the earliest allowed clock-in is <strong>' . $earliestClockIn->format('g:i A') . '</strong>.',
                    ]);
                }

                # For late clock in for am shift

                if ($timestamp->gt($latestClockIn) && $timestamp->lt($maxClockOut)) {
                    return $this->dispatch('showConfirmation', [
                        'title' => 'Are you sure to continue?',
                        'message' => 'You\'re clocking-in later than your expected time of <strong>' . $latestClockIn->format('g:i A') . '</strong>. This may be considered and marked as late.',
                        'action' => 'triggerClock',
                    ]);
                }

                if($timestamp->gte($maxClockOut)) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Please be informed!',
                        'message' => 'Unable to clock-in because it\'s already ' . $timestampFormatted,
                    ]);
                }

                return $this->dispatch('showConfirmation', [
                    'title' => 'Are you sure to continue?',
                    'message' => 'The action cannot be undone or reverted!',
                    'action' => 'triggerClock',
                ]);

            }

            $clockInTime = Carbon::parse($records->clock_in_am);
            $expectedClockOut = $clockInTime->copy()->addHours(9);  
            $expectedClockOutMins = $expectedClockOut->diffInMinutes($timestamp);
            
            if($shift->shift_duration == 'flexible') {
                if($expectedClockOut->gt($maxClockOut)) {
                    $formattedExpectedClockOut = $maxClockOut->format('g:i A'); 
                } else {
                    $formattedExpectedClockOut = $expectedClockOut->format('g:i A');
                }
            } else {
                $expectedClockOut = Carbon::parse($shift->end_shift);
                $formattedExpectedClockOut = $expectedClockOut->format('g:i A');
            }

            

            # If records has been populated

            # Handle AM Validation

            if($amOrPm == 'am') {

                if(is_null($records->clock_out_am) && $timestamp->lt($breakTimeFrom)) {
                    return $this->dispatch('showConfirmation', [
                        'title' => 'Are you sure to continue?',
                        'message' => 'You\'re attempting to break-out earlier than your expected time of <strong>' . $breakTimeFromFormatted . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'triggerClock',
                    ]);
                }

                if(is_null($records->clock_out_am) && $timestamp->lt($expectedClockOut) && $timestamp->hour != 12) {
                    return $this->dispatch('showConfirmation', [
                        'title' => 'Please be informed!,',
                        'plugin' => [
                            'textarea',
                            'title' => 'Please write your today\'s accomplishment report.',
                        ],
                        'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'triggerClock',
                    ]);  
                }

                if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && is_null($records->clock_in_pm) && $timestamp->lt($breakTimeFrom)) {
                    if($records->isUnderTime) {
                        return $this->dispatch('alert', [
                            'showAlert' => true,
                            'status' => 'info',
                            'title' => 'Please be informed!', 
                            'message' => 'We\'ve noticed that your break-out was earlier than expected time of <strong>' . $breakTimeFromFormatted . '</strong>. You\'re expected break-in is from <strong>' . $breakTimeFromFormatted . ' - ' . $breakTimeToFormatted . '</strong>'
                        ]);
                    }

                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Please be informed!', 
                        'message' => 'We\'ve noticed that your break-out was earlier than expected time of <strong>' . $breakTimeFromFormatted . '</strong>. You\'re expected break-in is from <strong>' . $breakTimeFromFormatted . ' - ' . $breakTimeToFormatted . '</strong>'
                    ]);
                }

            }

            # Handle PM Validation
            
            if($amOrPm == 'pm') {
              
                if(is_null($records->clock_out_pm) && $timestamp->lt($maxClockOut)) {
                    if($expectedClockOut->gt($maxClockOut)) {
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed!,',
                            'plugin' => [
                                'textarea',
                                'title' => 'Please write your today\'s accomplishment report.',
                            ],
                            'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);  
                    }

                    if(is_null($records->clock_in_am) && is_null($records->clock_out_am) && $timestamp->lt($expectedClockOut)) {
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed!,',
                            'plugin' => [
                                'textarea',
                                'title' => 'Please write your today\'s accomplishment report.',
                            ],
                            'message' => 'You\'re clocking-out earlier thans your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);  
                    }


                    if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm) && $timestamp->lte($expectedClockOut)) {
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed!,',
                            'plugin' => [
                                'textarea',
                                'title' => 'Please write your today\'s accomplishment report.',
                            ],
                            'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);  
                    }

                    if(!is_null($records->clock_in_am) && is_null($records->clock_out_am) && is_null($records->clock_in_pm) && is_null($records->clock_out_pm) && $timestamp->hour != 12) {
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed!,',
                            'plugin' => [
                                'textarea',
                                'title' => 'Please write your today\'s accomplishment report.',
                            ],
                            'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);  
                    }
                    
                }

            }

            if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                return $this->dispatch('showConfirmation', [
                    'title' => 'Before clocking out,',
                    'plugin' => [
                        'textarea',
                        'title' => ''
                    ],
                    'message' => 'Please write your today\'s accomplishment report.',
                    'action' => 'triggerClock',
                ]);  
            } else if(is_null($records->clock_in_am) && is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                return $this->dispatch('showConfirmation', [
                    'title' => 'Before clocking out,',
                    'plugin' => [
                        'textarea',
                        'title' => ''
                    ],
                    'message' => 'Please write your today\'s accomplishment report.',
                    'action' => 'triggerClock',
                ]);  
            } else if(!is_null($records->clock_out_pm)) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'warning',
                    'title' => 'Please be informed!',
                    'message' => 'You have completed your work hours today. No actions available for clock-in or clock-out.',
                ]);
            } else {
                return $this->dispatch('showConfirmation', [
                    'title' => 'Are you sure to continue?',
                    'message' => 'The action cannot be undone or reverted!',
                    'action' => 'triggerClock',
                ]);  
            }

        }
    
        $this->accomplishment = $data;

        // If notifications are disabled, proceed to capture the clock-in
        $this->dispatch('capture', ['isForcedClockout' => false]);
    }

    public function triggerClockOut(bool $isNotify = true, $data = null) {

        $shift = $this->employeeShift();

        if(is_null($shift)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Please be informed!',
                'message' => 'Your clock-in and clock-out actions cannot be processed as no shift schedule is currently assigned to you. Kindly contact HR for further assistance.',
            ]);
        }

        // $timestamp = Carbon::now();
        $timestamp = Carbon::parse($this->manipulate_timestamp);

        $amOrPm = strtolower($timestamp->format('A')); // AM or PM

        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', $timestamp)
            ->first();

        $endShift = Carbon::createFromTime(17, 0, 0);


        if($isNotify) {
            $clockInTime = Carbon::parse($records->clock_in_am);
            $expectedClockOut = $clockInTime->copy()->addHours(9);  
            $expectedClockOutMins = $expectedClockOut->diffInMinutes($timestamp);
            
            if($expectedClockOut->gt($endShift)) {
                $formattedExpectedClockOut = $endShift->format('g:i A'); 
            } else {
                $formattedExpectedClockOut = $expectedClockOut->format('g:i A');
            }

                return $this->dispatch('showConfirmation', [
                    'title' => 'Please be informed!,',
                    'plugin' => [
                        'textarea',
                        'title' => 'Please write your today\'s accomplishment report.',
                    ],
                    'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                    'action' => 'triggerClockOut',
            ]);      
        
        }

        $this->accomplishment = $data;
        $this->dispatch('capture', ['isForcedClockout' => true]);
    }
    
    public function isAlreadyClockInOrOut()
    {

        $shift = $this->employeeShift();

        $breakTimeTo = Carbon::parse($shift->break_in);
        $latestClockIn = Carbon::parse($shift->web_latest_clockin);

        // $timestamp = Carbon::now();
        $timestamp = Carbon::parse($this->manipulate_timestamp); // Assuming the current date is considered
        $amOrPm = strtolower($timestamp->format('A')); // AM or PM
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', $timestamp)
            ->first();

        if (!$records) {
            // If no records, perform clock-in
            return $this->clockin($amOrPm, $records, $timestamp);
        }

        if ($amOrPm == 'am') {
            // If the AM clock-in exists and no AM clock-out, clock-out AM
            if (!is_null($records->clock_in_am) && is_null($records->clock_out_am)) {
                return $this->clockout('am', $records, $timestamp);
            }
    
            // If AM clock-in is done and PM clock-in is not done yet, handle PM clock-in
            if (!is_null($records->clock_out_am) && is_null($records->clock_in_pm)) {
                return $this->clockin('pm', $records, $timestamp);
            }
    
            // If PM clock-in is already done and no PM clock-out, handle PM clock-out
            if (!is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                return $this->clockout('pm', $records, $timestamp);
            }

            // If no clock-in yet, perform AM clock-in
            return $this->clockin('am', $records, $timestamp);
        } 
        
        // For PM clock-in/out conditions
        elseif ($amOrPm == 'pm') {

            if(!is_null($records->clock_in_am) && is_null($records->clock_out_am) && $timestamp->gt($breakTimeTo)) {
                return $this->clockout('pm', $records, $timestamp);
            }
            
            if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && is_null($records->clock_in_pm)) {
                return $this->clockin('pm', $records, $timestamp);
            }

            // Ensure PM Clock-In happens only if AM Clock-Out is done and no PM Clock-In has occurred            
            if(!is_null($records->clock_in_am) && is_null($records->clock_out_am)) {
                if($timestamp->hour == 12) {
                    return $this->clockout('am', $records, $timestamp);
                } else {
                    return $this->clockout('pm', $records, $timestamp);
                }
            }

            if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                return $this->clockout('pm', $records, $timestamp);
            }

            if(is_null($records->clock_in_am) && is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
                return $this->clockout('pm', $records, $timestamp);
            }
            
        } 

    }
   
    private function clockin($shift, $records, $timestamp) {
        $location = $this->saveLocation();
    
        // Parse and format the timestamp
        $formattedTimestamp = Carbon::parse($timestamp)->format('g:i A');
    
        // Initialize the columns to be updated
        $columns = [
            'bsd_no' => $this->bsd_no,
            'employee_no' => $this->user_id,
            'origin' => 'web',
            'captured_image_clockin' => $this->capturedImage,
            'captured_location_clockin' => $location,
        ];
    
        // Check if it's AM or PM shift and set clock-in times
        if ($shift == 'am') {
            $columns['clock_in_am'] = $formattedTimestamp;
        } else {
            $columns['clock_in_pm'] = $formattedTimestamp;
        }
    
        // Reset AM shift clock-in/clock-out times if PM shift is selected and no records exist
        if ($shift == 'pm' && is_null($records)) {
            $columns['clock_in_am'] = null;
            $columns['clock_out_am'] = null;
        }
    
        // Create or update the clock-in record
        if (!$records) {
            EmployeeClockInOut::create($columns);
        } else {
            $records->update($columns);
        }
    
        // Retrieve the updated record
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', Carbon::parse($timestamp)->toDateString())
            ->first();
    
        // Toggle the status (e.g., late or on-time)
        $this->toggleStatus($records);
        
        $reminder = '';

        // Dispatch success alert
        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Yey!',
            'message' => 'Your action has been successfully documented and recorded in the system for future reference.',
        ]);
    }
    
    private function clockout($shift, $records, $timestamp)
    {
        // Determine the default columns for clock-in, clock-out
        $clockInColumn = $shift == 'am' ? 'clock_in_am' : 'clock_in_pm';
        $clockOutColumn = $shift == 'am' ? 'clock_out_am' : 'clock_out_pm';

        $location = $this->saveLocation();

        // Default to current timestamp if isForcedClockout is true and clock-out or clock-in fields are missing
        if ($this->isForcedClockout) {
            // If clock_out_am is not empty but clock_in_pm is empty, use clock_out_am and current timestamp
            if (!empty($records->clock_out_am) && empty($records->clock_in_pm)) {
                // Set the clock_out_pm to current timestamp
                $clockOutTime = $timestamp; // Use current timestamp as clock-out time
            }
            // If clock_out_am or clock_in_pm is null/empty, use clock_in_am and clock_out_pm for computation
            elseif (empty($records->clock_out_pm) || empty($records->clock_in_am)) {
                // Use clock_in_am and the current timestamp for clock_out_pm
                $clockInTime = Carbon::parse($records->clock_in_am);  // Use clock_in_am
                $clockOutTime = $timestamp; // Use current timestamp as clock_out_pm
            } else {
                // Regular clock_out_pm value if no forced clock-out and both times are available
                $clockOutTime = Carbon::parse($records->clock_out_pm);  
            }

            $clockOutColumn = 'clock_out_pm';

            $records->update([
                'employee_no' => $this->user_id,
                'captured_image_clockout' => $this->capturedImage,
                'captured_location_clockout' => $location,
                $clockOutColumn => $clockOutTime->format('g:i A'), // Update the correct clock-out field
            ]);

        } else {
            // If not forced, just use the current timestamp as clock-out time
            $clockOutTime = Carbon::parse($timestamp);  // Regular clock-out time
             // Update the record with the calculated values
            $records->update([
                'employee_no' => $this->user_id,
                'captured_image_clockout' => $this->capturedImage,
                'captured_location_clockout' => $location,
                $clockOutColumn => $clockOutTime->format('g:i A'), // Update the correct clock-out field
            ]);
        }


        // Update the record with the calculated total consumed, overtime, and overall minutes
        $records->update([
            'bsd_no' => $this->bsd_no,
            'accomplishment' => $this->accomplishment['report'] ?? ''
        ]);

        $this->toggleStatus($records);

        // Adjust message dynamically based on shift

        // Dispatch success alert
        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Yey!',
            'message' => 'Your action has been successfully documented and recorded in the system for future reference.',
        ]);
    }

    public function processClock($imageData, $isImageCaptured, $isForcedClockout) {

        $this->isImageCaptured = $isImageCaptured;
        $this->isForcedClockout = $isForcedClockout;

        if (!$isImageCaptured) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Oops',
                'message' => 'Unable to clock in or clock out due to the absence of a captured image. Please ensure that your browser\'s camera is enabled and positioned to capture your face clearly.'
            ]);
        }

        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = $this->user_id . '_' . time() . '.png';

        $this->capturedImage = $imageName;
        Storage::disk('public')->put('clockinout/' . $imageName, base64_decode($image));

        if($isForcedClockout) {

            // $timestamp = Carbon::now();
            $timestamp = Carbon::parse($this->manipulate_timestamp);
            $amOrPm = strtolower($timestamp->format('A'));

            $records = EmployeeClockInOut::where('employee_no', $this->user_id)
                ->whereDate('created_at', $timestamp)
                ->first();

            $this->clockout($amOrPm, $records, $timestamp);

        } else {    
            $this->isAlreadyClockInOrOut();
        }
    }
    
    public function shiftSchedule() {
        return ShiftSchedule::first() ?? null;
    }

    public function showLogs() {
        $records = EmployeeClockInOut::where('bsd_no', $this->bsd_no)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        $this->logs = $records;
        
        $this->dispatch('showModal', [
            'modal' => 'logs_modal'
        ]);

    }

    public function saveLocation() {
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

    public function render()
    {
        return view('livewire.employee.clock');
    }
}