<?php

namespace App\Livewire\Admin\Settings\ShiftSchedule;

use App\Models\CompanyInformation;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public $records;
    public $organization;

    public array $fields;
    public $clockinMobTimes;
    public $clockinWebTimes;
    
    public function mount() {
        $this->loadRecords();
    }
    
    public function loadRecords() {
        $this->organization = CompanyInformation::first();
        $this->clockinMobTimes = $this->originalClockinTimes();
        $this->clockinWebTimes = $this->originalClockinTimes();

        $records = ShiftSchedule::first();

        $this->fields = [
            'mobile_earliest_clockin' => $records->mobile_earliest_clockin ?? null,
            'mobile_latest_clockin' => $records->mobile_latest_clockin ?? null,
            'web_earliest_clockin' => $records->web_earliest_clockin ?? null,
            'web_latest_clockin' => $records->web_latest_clockin ?? null,
            'min_ot_mins' => $records->min_ot_mins ?? null,
            'max_ot_time' => $records->max_ot_time ?? null,
            'is_late_strict' => $records && $records->is_late_strict !== null ? ($records->is_late_strict ? 'yes' : 'no') : null,
            'is_strict_undertime' => $records && $records->is_strict_undertime !== null ? ($records->is_strict_undertime ? 'yes' : 'no') : null,
        ];

        $this->updateClockOutTimes('mobile');
        $this->updateClockOutTimes('web');

    }
    
    public function updateField($propertyName) {
        if (in_array($propertyName, ['mobile', 'web'])) {
            $this->updateClockinTimes($propertyName);
            $this->fields[$propertyName . '_expected_clockout'] = '';
            $this->fields[$propertyName . '_latest_clockin'] = '';
        }
    
        if (in_array($propertyName, ['mobile_out', 'web_out'])) {
            $type = str_replace('_out', '', $propertyName); // Extract 'mobile' or 'web'
            $this->updateClockOutTimes($type);
        }
    }

    private function updateClockinTimes($propertyName) {
        // Determine the earliest clock-in time based on the property (mobile or web)
        $earliestClockIn = (int) $this->fields[$propertyName . '_earliest_clockin'];
    
        // If a valid earliest clock-in time is selected, filter clock-in times
        if ($earliestClockIn) {
            // Dynamically update the appropriate clock-in times (mobile or web)
            if ($propertyName == 'mobile') {
                $this->clockinMobTimes = [];
                foreach ($this->originalClockinTimes() as $key => $time) {
                    if ($key >= $earliestClockIn) {
                        $this->clockinMobTimes[$key] = $time;
                    }
                }
            } elseif ($propertyName == 'web') {
                $this->clockinWebTimes = [];
                foreach ($this->originalClockinTimes() as $key => $time) {
                    if ($key >= $earliestClockIn) {
                        $this->clockinWebTimes[$key] = $time;
                    }
                }
            }
        }
    }

    private function updateClockOutTimes($type) {
        // Determine the type ('mobile' or 'web') and fetch corresponding clock-in times
        $earliestClockIn = (int) $this->fields[$type . '_earliest_clockin'];
        $latestClockIn = (int) $this->fields[$type . '_latest_clockin'];
    
        // Ensure both earliest and latest clock-in are valid and latest is greater than or equal to earliest
        if ($earliestClockIn && $latestClockIn && $earliestClockIn <= $latestClockIn) {
            // Add 8 working hours + 1-hour break (9 total hours) to calculate clock-out
            $earliestClockOut = $earliestClockIn + 9;
            $latestClockOut = $latestClockIn + 9;
    
            // Format the clock-out times in AM/PM format
            $earliestClockOutFormatted = $this->formatTimeToAMPM($earliestClockOut);
            $latestClockOutFormatted = $this->formatTimeToAMPM($latestClockOut);
    
            // Store the computed expected clock-out times for the specific type
            if ($earliestClockOut === $latestClockOut) {
                $this->fields[$type . '_expected_clockout'] = $earliestClockOutFormatted;
            } else {
                $this->fields[$type . '_expected_clockout'] = $earliestClockOutFormatted . ' - ' . $latestClockOutFormatted;
            }
        } else {
            // Reset or clear the clock-out time if conditions are not met
            $this->fields[$type . '_expected_clockout'] = '';
        }
    }
    
    private function formatTimeToAMPM($time) {
        $hours = $time % 24;
        $period = $hours >= 12 ? 'PM' : 'AM';
        $formattedHour = $hours > 12 ? $hours - 12 : ($hours == 0 ? 12 : $hours);
    
        return $formattedHour . ':00 ' . $period;
    }    
    
    private function originalClockinTimes() {
        return [
            7 => '7:00 AM',
            8 => '8:00 AM',
            9 => '9:00 AM',
        ];
    }

    protected function rules() {
        return [
            'fields.mobile_earliest_clockin' => 'required|integer|min:0|max:23',
            'fields.mobile_latest_clockin' => 'required|integer|min:0|max:23',
            'fields.web_earliest_clockin' => 'required|integer|min:0|max:23',
            'fields.web_latest_clockin' => 'required|integer|min:0|max:23',
            
            'fields.min_ot_mins' => 'required|integer',
            'fields.max_ot_time' => 'required|integer|gte:8|lte:12',
            'fields.is_late_strict' => 'required|in:yes,no',
            'fields.is_strict_undertime' => 'required|in:yes,no',
        ];
    }

    protected function messages() {
        return [
            'fields.mobile_earliest_clockin.required' => 'Mobile earliest clock-in time is required.',
            'fields.mobile_earliest_clockin.integer' => 'Mobile earliest clock-in must be a valid hour (0-23).',
            'fields.mobile_earliest_clockin.min' => 'Mobile earliest clock-in must be at least 0.',
            'fields.mobile_earliest_clockin.max' => 'Mobile earliest clock-in must be at most 23.',
    
            'fields.mobile_latest_clockin.required' => 'Mobile latest clock-in time is required.',
            'fields.mobile_latest_clockin.integer' => 'Mobile latest clock-in must be a valid hour (0-23).',
            'fields.mobile_latest_clockin.min' => 'Mobile latest clock-in must be at least 0.',
            'fields.mobile_latest_clockin.max' => 'Mobile latest clock-in must be at most 23.',
    
            'fields.web_earliest_clockin.required' => 'Web earliest clock-in time is required.',
            'fields.web_earliest_clockin.integer' => 'Web earliest clock-in must be a valid hour (0-23).',
            'fields.web_earliest_clockin.min' => 'Web earliest clock-in must be at least 0.',
            'fields.web_earliest_clockin.max' => 'Web earliest clock-in must be at most 23.',
    
            'fields.web_latest_clockin.required' => 'Web latest clock-in time is required.',
            'fields.web_latest_clockin.integer' => 'Web latest clock-in must be a valid hour (0-23).',
            'fields.web_latest_clockin.min' => 'Web latest clock-in must be at least 0.',
            'fields.web_latest_clockin.max' => 'Web latest clock-in must be at most 23.',
    
            'fields.min_ot_mins.required' => 'Minimum overtime hours are required.',
            'fields.min_ot_mins.integer' => 'Minimum overtime hours must be an integer.',
    
            'fields.max_ot_time.required' => 'Maximum overtime time is required.',
            'fields.max_ot_time.integer' => 'Maximum overtime time must be an integer.',
            'fields.max_ot_time.gte' => 'Overtime must be at least 8 hours.',
            'fields.max_ot_time.lte' => 'Overtime must be no more than 12 hours.',
            
            'fields.is_late_strict.in' => 'Choose between yes or no for strict lateness.',
            'fields.is_strict_undertime.in' => 'Choose between yes or no for strict undertime.',
        ];
    }
    

    public function save() {

        $this->validate();

        try {

            $shift = ShiftSchedule::where('id', 1)->first();
    
            if ($shift) {
                // If a record exists, update it
                $shift->update([
                    'mobile_earliest_clockin' => $this->fields['mobile_earliest_clockin'] ?? null,
                    'mobile_latest_clockin' => $this->fields['mobile_latest_clockin'] ?? null,
                    'web_earliest_clockin' => $this->fields['web_earliest_clockin'] ?? null,
                    'web_latest_clockin' => $this->fields['web_latest_clockin'] ?? null,
                    'min_ot_mins' => $this->fields['min_ot_mins'] ?? null,
                    'max_ot_time' => $this->fields['max_ot_time'] ?? null,
                    'is_late_strict' => $this->fields['is_late_strict'] ?? null,
                    'is_strict_undertime' => $this->fields['is_strict_undertime'] ?? null,
                ]);
            } else {
                // If no record exists, create a new one
                ShiftSchedule::create([
                    'mobile_earliest_clockin' => $this->fields['mobile_earliest_clockin'] ?? null,
                    'mobile_latest_clockin' => $this->fields['mobile_latest_clockin'] ?? null,
                    'web_earliest_clockin' => $this->fields['web_earliest_clockin'] ?? null,
                    'web_latest_clockin' => $this->fields['web_latest_clockin'] ?? null,
                    'min_ot_mins' => $this->fields['min_ot_mins'] ?? null,
                    'max_ot_time' => $this->fields['max_ot_time'] ?? null,
                    'is_late_strict' => $this->fields['is_late_strict'] ? true : false,
                    'is_strict_undertime' => $this->fields['is_strict_undertime'] ? true : false,
                ]);
            }
    
            DB::commit(); // Commit the transaction
    
            // Dispatch success alert
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Shift schedule was updated.'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
    
            // Dispatch error alert
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'showAlert' => true,
                'message' => 'Error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.settings.shift-schedule.index');
    }
}
