<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
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
    public $isClockedIn = false;
    public $isClockedOut = false;
    public $isImageCaptured = false;
    public $capturedImage;
    public $capturedLocation;
    public $logs;
    public $isLate = false;
    public $isUndertime = false;
    public $isHalfDay = false;

    public $status;

    protected $listeners = ['triggerClock'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.clock');
        };

        $this->user_id = $user_id;

        $timestamp = Carbon::today();

        $records = EmployeeClockInOut::where('employee_no', $user_id)
            ->whereDate('created_at', $timestamp)->first();

        $this->toggleStatus($records);
    }

    public function toggleStatus($records)
    {

        $shift = $this->shiftSchedule();

        $breakTimeFrom = Carbon::createFromTime($shift->break_from == 12 ? 12 : $shift->break_from + 12, 0, 0);
        $breakTimeTo = Carbon::createFromTime($shift->break_to + ($shift->break_to >= 1 ? 12 : 0), 0, 0);

        $timestamp = Carbon::now();

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

    
    public function triggerClock(bool $isNotify = true)
    {
        $shift = $this->shiftSchedule();
        
        // Times for earliest and latest clock-in, break times
        $earliestClockIn = Carbon::createFromTime($shift->web_earliest_clockin, 0, 0);
        $latestClockIn = Carbon::createFromTime($shift->web_latest_clockin, 0, 0);
        
        $breakTimeFrom = Carbon::createFromTime($shift->break_from == 12 ? 12 : $shift->break_from + 12, 0, 0);
        $breakTimeFromFormatted = $breakTimeFrom->format('g:i A');
        
        $breakTimeTo = Carbon::createFromTime($shift->break_to + ($shift->break_to >= 1 ? 12 : 0), 0, 0);
        $breakTimeToFormatted = $breakTimeTo->format('g:i A');

        // Current time (timestamp)
        $timestamp = Carbon::now();
        $timestampFormatted = $timestamp->format('g:i A');

        $amOrPm = strtolower($timestamp->format('A')); // AM or PM
        
        // Get the clock-in/out records for the current employee
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', $timestamp)
            ->first();


        if ($records && $records->clock_in_am) {
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
                        'title' => 'Please be informed',
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
                
                if(is_null($records->clock_out_pm) && $timestamp->lt($expectedClockOut)) {
                    if($expectedClockOut->gt($maxClockOut) && $records->isLate) {
                        $this->isUndertime = true;
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed',
                            'message' => 'We\'ve noticed that you\'re clock-in time was late, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);
                    }

                    if(is_null($records->clock_in_am) && is_null($records->clock_out_am) && $timestamp->lt($expectedClockOut) && $records->isLate) {
                        $this->isUndertime = true;
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed',
                            'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);
                    }


                    if(!is_null($records->clock_in_am) && !is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm) && $timestamp->lt($expectedClockOut)) {
                        $this->isUndertime = true;
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed',
                            'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);
                    }

                    if(!is_null($records->clock_in_am) && is_null($records->clock_out_am) && is_null($records->clock_in_pm) && is_null($records->clock_out_pm) && $timestamp->hour != 12) {
                        $this->isUndertime = true;
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be informed',
                            'message' => 'You\'re clocking-out earlier than your expected time of <strong>' . $formattedExpectedClockOut . '</strong>, which may be considered and marked as undertime.',
                            'action' => 'triggerClock',
                        ]);
                    }
                    
                }

            }

            if(is_null($records->clock_out_pm)) {
                return $this->dispatch('showConfirmation', [
                    'title' => 'Are you sure to continue?',
                    'message' => 'The action cannot be undone or reverted!',
                    'action' => 'triggerClock',
                ]);
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'warning',
                    'title' => 'Please be informed!',
                    'message' => 'You have completed your work hours today. No actions available for clock-in or clock-out.',
                ]);
            }

        }
    
        // If notifications are disabled, proceed to capture the clock-in
        $this->dispatch('capture');
    }
    
    public function isAlreadyClockInOrOut()
    {
        $shift = $this->shiftSchedule();
        $breakTimeTo = Carbon::createFromTime($shift->break_to + ($shift->break_to >= 1 ? 12 : 0), 0, 0);
        $latestClockIn = Carbon::createFromTime($shift->web_latest_clockin, 0, 0);

        $timestamp = Carbon::now(); // Assuming the current date is considered
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

        if(!$records) {
            // Clock in based on AM or PM shift
            EmployeeClockInOut::create([
                'employee_no' => $this->user_id,
                'origin' => 'web',
                'captured_image_clockin' => $this->capturedImage,
                'captured_location_clockin' => $location,
                'isLate' => $this->isLate,
                // Conditionally set the clock in time depending on the shift
                $shift == 'am' ? 'clock_in_am' : 'clock_in_pm' => Carbon::parse($timestamp)->format('g:i A')
            ]);
        } else {
            // Clock in based on AM or PM shift
            $records->update([
                'employee_no' => $this->user_id,
                'isLate' => $this->isLate,
                // Conditionally set the clock in time depending on the shift
                $shift == 'am' ? 'clock_in_am' : 'clock_in_pm' => Carbon::parse($timestamp)->format('g:i A')
            ]);
        }

        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', $timestamp)
            ->first();
    
        $this->toggleStatus($records);

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

        $shift = $this->shiftSchedule();

        $location = $this->saveLocation();

        // Calculate break time to handle the condition for switching to PM clock-out
        $breakTimeFrom = Carbon::createFromTime($shift->break_from == 12 ? 12 : $shift->break_from + 12, 0, 0);
        $breakTimeTo = Carbon::createFromTime($shift->break_to + ($shift->break_to >= 1 ? 12 : 0), 0, 0);
        
        // Add condition for handling the clock-out logic based on break time
        if (!is_null($records->clock_in_am) && is_null($records->clock_out_am) && $timestamp->gt($breakTimeTo)) {
            // If the condition is met, update to PM clock-out column and calculate hours from AM clock-in to current time
            $clockOutColumn = 'clock_out_pm'; // Switch to PM clock-out column
            $hoursConsumedColumn = 'mins_consumed_pm'; // Use PM hours consumed column
            $clockInTime = Carbon::parse($records->clock_in_am); // Use clock-in AM time for calculation
            $isMinusOneHour = true;
        }
    
        // Special case: if it's 12 PM and clock-in for AM is missing and clock-in for PM exists, update the PM clock-out
        if ($timestamp->hour == 12 && is_null($records->clock_in_am) && is_null($records->clock_out_am) && !is_null($records->clock_in_pm) && is_null($records->clock_out_pm)) {
            // If conditions match, update the PM clock-out column instead of AM
            $clockOutColumn = 'clock_out_pm'; // Use PM clock-out column
            $hoursConsumedColumn = 'mins_consumed_pm'; // Use PM hours consumed column
        }
    
        // Handle special case: if it's PM and AM clock-out is missing, update AM fields
        if ($shift == 'pm' && $records->clock_out_am === null && $records->clock_in_pm == null) {
            // If it's PM, but AM hasn't been clocked out, switch to AM clock-out fields
            $clockOutColumn = 'clock_out_am'; // Use AM clock-out column
            $hoursConsumedColumn = 'mins_consumed_am'; // Use AM hours consumed column
        }
    
        // Calculate the minutes rendered by comparing clock-in time and the given timestamp
        $minutesRendered = $clockInTime->diffInMinutes($timestamp);

        // If $isMinusOneHour is true, subtract 60 minutes
        if ($isMinusOneHour) {
            $minutesRendered -= 60; // Subtract 60 minutes if condition is true
        }

        // Check if the clock_in_pm is between breakTimeFrom and breakTimeTo
        if ($clockInTime->between($breakTimeFrom, $breakTimeTo) && $timestamp->gt($breakTimeTo)) {
            // If it is, calculate the minutes from $breakTimeTo to the timestamp
            $minutesRendered = $breakTimeTo->diffInMinutes($timestamp);
        }

        // Ensure that the minutes rendered are not negative (in case the breaktimeTo is after the timestamp)
        $minutesRendered = max(0, $minutesRendered);

        
        // Update the record with the calculated values
        $records->update([
            'employee_no' => $this->user_id,
            $hoursConsumedColumn => $minutesRendered, // Update the correct hours consumed field
            'captured_image_clockout' => $this->capturedImage,
            'captured_location_clockout' => $location,
            'isUnderTime' => $this->isUndertime,
            $clockOutColumn => Carbon::parse($timestamp)->format('g:i A'), // Update the correct clock-out field
        ]);

        // Calculate total minutes consumed (AM and PM combined)
        $minsAm = (int) $records->mins_consumed_am ?? 0;
        $minsPm = (int) $records->mins_consumed_pm ?? 0;


        //  if employee start working pm shift only
        if ($minsAm == 0 && $minsPm > 0) {

            $endShift = Carbon::createFromTime(17, 0, 0); // 5:00 PM
            
            if(is_null($records->clock_in_am) && is_null($records->mins_consumed_am)) {
                $clockin = Carbon::parse($records->clock_in_pm);
                $clockout = Carbon::parse($records->clock_out_pm); 

                $regMins = $clockout->diffInMinutes($clockin);

                if($clockin->between($breakTimeFrom, $breakTimeTo)) {
                    $regMins -= 60;
                } 
            } 
            
            if (!is_null($records->clock_in_am) && is_null($records->mins_consumed_am)) {
                $clockin = Carbon::parse($records->clock_in_am);
                $clockout = Carbon::parse($records->clock_out_pm); 

                $regMins = $clockout->diffInMinutes($clockin);

                if($clockin->between($breakTimeFrom, $breakTimeTo)) {
                    $regMins -= 60;
                } 

            }

            if(!is_null($records->clock_out_am) && !is_null($records->clock_in_pm)) {
                if($clockout->gt($endShift)) {
                    $regMins = $clockout->diffInMinutes($endShift);
                    if($clockin->between($breakTimeFrom, $breakTimeTo)) {
                        $regMins += 60;
                    } 
                }
            } else {
                if($clockout->gt($endShift)) {
                    $minsOT = $clockout->diffInMinutes($endShift);
                    $regMins = $regMins - $minsOT; 
                } else {
                    $regMins = $regMins;
                }
            }

                  
            // Calculate overtime minutes (if any)
            $minsOT = ($clockout->gt($endShift)) ? $clockout->diffInMinutes($clockout->copy()->setTime(17, 0)) : 0;
        
            // Calculate overall minutes (regular + overtime)
            $overallMins = $regMins + $minsOT;
        }
        

        //  if employee start working from am shift to afternoon
        if($minsAm > 0 && $minsPm > 0) {
            
            $expectedClockOut = Carbon::parse($records->clock_in_am)->addHours(9);
            $endShift = Carbon::createFromTime($expectedClockOut->hour, $expectedClockOut->minute, $expectedClockOut->second);

            $clockin = Carbon::parse($records->clock_in_am);
            $clockout = Carbon::parse($records->clock_out_pm); // Clock-out time
            
            // Regular minutes
     
            $totalConsumedHrs= $records->mins_consumed_am + $records->mins_consumed_pm;

     
            if($clockout->gt($endShift)) {
                $minsOT = $clockout->diffInMinutes($endShift);
                $regMins = $totalConsumedHrs - $minsOT; 
            } else {
                $regMins = $totalConsumedHrs;
            }

            // Calculate overtime minutes (if any)
            // $minsOT = ($clockout->gt($endShift)) ? $clockout->diffInMinutes($clockout->copy()->setTime(17, 0)) : 0;
        
            // Calculate overall minutes (regular + overtime)
            $overallMins = $regMins + ($minsOT ?? 0);

        }


        // Update the record with calculated values
        $records->update([
            'employee_no' => $this->user_id,
            'total_mins_consumed' => $regMins ?? 0, // Regular minutes
            'mins_ot' => $minsOT ?? 0,              // Overtime minutes
            'overall_mins' => $overallMins ?? 0,    // Total minutes including overtime
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
    
    public function processClock($imageData, $isImageCaptured) {

        $this->isImageCaptured = $isImageCaptured;

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

        $this->isAlreadyClockInOrOut();
    }
    
    public function shiftSchedule() {
        return ShiftSchedule::first() ?? null;
    }

    public function showLogs() {
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
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