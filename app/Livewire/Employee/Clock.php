<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
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
    public $isLate = false;
    public $isUndertime = false;
    public $isHalfDay = false;
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
                'message' => 'Your clock-in and clock-out actions cannot be processed as no shift schedule is currently assigned to you. Kindly contact HR for further assistance.',
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


        if ($records && $records->clock_in_am !== $records->clock_out_am && $records->clock_in_am !== $records->clock_in_pm) {
            $clockInTime = $records->clock_in_am;
            $maxClockOut = Carbon::parse($clockInTime)->addHours(9);
        } else {
            $maxClockOut = Carbon::createFromTime(17, 0, 0);
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
                    $this->isLate = true;
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
            
            if($expectedClockOut->gt($maxClockOut)) {
                $formattedExpectedClockOut = $maxClockOut->format('g:i A'); 
            } else {
                $formattedExpectedClockOut = $expectedClockOut->format('g:i A');
            }

            # If records has been populated

            # Handle AM Validation

            if($amOrPm == 'am') {

                # If am shift already populated and timestamp has been altered

                if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && is_null($records->clock_out_pm) && $timestamp->lt($breakTimeTo) && !$records->isUnderTime) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Invalid Timestamp', 
                        'message' => 'We\'ve noticed invalid timestamp and altering the handling of timekeeping!'
                    ]);
                }

                if(is_null($records->clock_out_am) && $timestamp->lt($breakTimeFrom)) {
                    $this->isUndertime = true;
                    return $this->dispatch('showConfirmation', [
                        'title' => 'Are you sure to continue?',
                        'message' => 'You\'re attempting to break-out earlier than your expected time of <strong>' . $breakTimeFromFormatted . '</strong>, which may be considered and marked as undertime.',
                        'action' => 'triggerClock',
                    ]);
                }

                if(is_null($records->clock_out_am) && $timestamp->lt($expectedClockOut) && $timestamp->hour != 12) {
                    $this->isUndertime = true;
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
                        'message' => 'The expected break-in is from <strong>' . $breakTimeFromFormatted . ' - ' . $breakTimeToFormatted . '</strong>'
                    ]);
                }

            }

            # Handle PM Validation
            
            if($amOrPm == 'pm') {
              
                if(is_null($records->clock_out_pm) && $timestamp->lt($maxClockOut)) {
                    if($expectedClockOut->gt($maxClockOut)) {
                        $this->isUndertime = true;
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
                        $this->isUndertime = true;
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
                        $this->isUndertime = true;
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
                        $this->isUndertime = true;
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

            $this->isUndertime = true;
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
        $timestamp = Carbon::parse($timestamp)->format('g:i A');
    
        // Initialize the columns to be updated
        $columns = [
            'employee_no' => $this->user_id,
            'origin' => 'web',
            'captured_image_clockin' => $this->capturedImage,
            'captured_location_clockin' => $location,
            'isLate' => $this->isLate,
        ];
    
        // Check if it's AM or PM shift and set clock-in times
        if ($shift == 'am') {
            $columns['clock_in_am'] = $timestamp;
        } else {
            $columns['clock_in_pm'] = $timestamp;
        }

        if ($shift == 'pm' && is_null($records)) {
            $columns['clock_in_am'] = null;
            $columns['clock_out_am'] = null;
        }
    
        if (!$records) {
            // Create a new clock-in record if no existing records found
            EmployeeClockInOut::create($columns);
        } else {
            // Update the existing record with the new clock-in information
            $records->update($columns);
        }
    
        // Retrieve the updated record
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', Carbon::parse($timestamp)->toDateString())
            ->first();
        
        // Toggle the status (e.g., late or on-time)
        $this->toggleStatus($records);
    
        // Dispatch success alert
        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Yey!',
            'message' => 'You\'re clocked in at ' . Carbon::parse($timestamp)->format('M d, Y h:i A')
        ]);
    }
    
    private function clockout($shift, $records, $timestamp)
    {
        $isMinusOneHour = false;

        // Determine the default columns for clock-in, clock-out, and hours consumed
        $clockInColumn = $shift == 'am' ? 'clock_in_am' : 'clock_in_pm';
        $clockOutColumn = $shift == 'am' ? 'clock_out_am' : 'clock_out_pm';
        $hoursConsumedColumn = $shift == 'am' ? 'mins_consumed_am' : 'mins_consumed_pm';

        // Parse the clock-in time
        $clockInTime = Carbon::parse($records->$clockInColumn);

        $shiftDetails = $this->employeeShift();
        $location = $this->saveLocation();

        // Define break times
        $breakTimeFrom = Carbon::parse($shiftDetails->break_out ?? '12:00:00');
        $breakTimeTo = Carbon::parse($shiftDetails->break_in ?? '13:00:00');

        // Default to current timestamp if isForcedClockout is true and clock-out or clock-in fields are missing
        if ($this->isForcedClockout) {
            // Check if clock_out_am is not empty but clock_in_pm is empty
            if (!empty($records->clock_out_am) && empty($records->$clockInColumn)) {
                // Use clock_out_am and current timestamp for computation
                $clockInTime = Carbon::parse($records->clock_out_am);  // Use clock_out_am as clock-in time
                $clockOutTime = $timestamp; // Use current timestamp as clock-out time
            } 
            // Check if clock_out_am or clock_in_pm is null/empty
            elseif (empty($records->$clockOutColumn) || empty($records->$clockInColumn)) {
                // Use clock_in_am and clock_out_pm for computation
                $clockInTime = Carbon::parse($records->clock_in_am);  // Use clock_in_am
                $clockOutTime = $timestamp; // Use clock_out_pm
            } else {
                $clockOutTime = Carbon::parse($records->$clockOutColumn);  // Regular clock_out field
            }
        } else {
            $clockOutTime = Carbon::parse($timestamp);  // Regular clock-out time
        }

        // Calculate the minutes rendered
        $minutesRendered = $clockInTime->diffInMinutes($clockOutTime);

        // Adjust for break time overlap
        if ($clockInTime->lt($breakTimeTo) && $clockOutTime->gt($breakTimeFrom)) {
            $breakOverlapStart = $clockInTime->lt($breakTimeFrom) ? $breakTimeFrom : $clockInTime;
            $breakOverlapEnd = $clockOutTime->gt($breakTimeTo) ? $breakTimeTo : $clockOutTime;
            $breakDuration = $breakOverlapStart->diffInMinutes($breakOverlapEnd);
            $minutesRendered -= $breakDuration;
        }

        // Ensure minutesRendered is not negative
        $minutesRendered = max(0, $minutesRendered);

        // Check if the worked time is greater than 8 hours
        $standardWorkHours = 480; // 8 hours = 480 minutes
        $overtimeMins = max(0, $minutesRendered - $standardWorkHours);
        
        // If overtime exists, adjust minutes
        $workedMinutes = $minutesRendered - $overtimeMins;

        // Update the record with the calculated values
        $records->update([
            'employee_no' => $this->user_id,
            'captured_image_clockout' => $this->capturedImage,
            'captured_location_clockout' => $location,
            'isUnderTime' => $this->isUndertime,
            $hoursConsumedColumn => $workedMinutes, // Update the correct hours consumed field
            $clockOutColumn => $clockOutTime->format('g:i A'), // Update the correct clock-out field
        ]);

        // Calculate total consumed minutes for the day
        $minsAm = (int) $records->mins_consumed_am ?? 0;
        $minsPm = (int) $records->mins_consumed_pm ?? 0;

        // Calculate the regular work hours for PM shift (until 5:00 PM)
        $standardEndTime = Carbon::parse($records->clock_in_am)->addHours(9); // 5:00 PM

        if ($records->clock_in_pm && $records->clock_out_pm) {
            // Parse clock-in and clock-out times for the PM shift
            $clockInPmTime = Carbon::parse($records->clock_in_pm);
            $clockOutPmTime = Carbon::parse($records->clock_out_pm);

            // Adjust clockInPmTime to start at or after breakTimeTo (1:00 PM)
            if ($clockInPmTime->lt($breakTimeTo)) {
                $clockInPmTime = $breakTimeTo; // Adjust clock-in time to start at 1:00 PM
            }

            // Regular work hours is the time between 1:00 PM and 5:00 PM
            $regularWorkTime = $clockInPmTime->diffInMinutes($standardEndTime->min($clockOutPmTime));
            $overtimeMinsPm = max(0, $clockOutPmTime->diffInMinutes($standardEndTime));

            // Update minsPm with regular work hours and OT separately
            $minsPm = $regularWorkTime;
            $overtimeMins += $overtimeMinsPm; // Add overtime minutes
        }

        // Calculate overall minutes, including OT
        $overallMins = $minsAm + $minsPm + $overtimeMins; // Total minutes including OT

        // Only set totalConsumedMins to 480 if total exceeds 480
        $totalConsumedMins = $minsAm + $minsPm + $overtimeMins;
        if ($totalConsumedMins > 480) {
            $totalConsumedMins = 480;
        }

        if(is_null($records->clock_in_am) && is_null($records->clock_out_am)) {
            $totalConsumedMins = null;
            $overallMins = null;
            $overtimeMins = null;
            $minsAm = null;
            $minsPm = null;
        }

        // Update the record with the calculated total consumed, overtime, and overall minutes
        $records->update([
            'bsd_no' => $this->bsd_no,
            'total_mins_consumed' => $totalConsumedMins, // Total worked minutes (capped at 480)
            'mins_ot' => $overtimeMins, // OT minutes
            'overall_mins' => $overallMins, // Total including OT
            'mins_consumed_am' => $minsAm,
            'mins_consumed_pm' => $minsPm, // Update the PM minutes after deducting OT
            'accomplishment' => $this->accomplishment['report'] ?? ''
        ]);

        $this->toggleStatus($records);

        // Dispatch success alert
        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Yey!',
            'message' => 'You\'re clocked out at ' . $timestamp->format('M d, Y h:i A'),
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
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
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