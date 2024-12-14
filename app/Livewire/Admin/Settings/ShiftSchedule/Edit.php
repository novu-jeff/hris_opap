<?php

namespace App\Livewire\Admin\Settings\ShiftSchedule;

use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Livewire\Component;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;

class Edit extends Component
{

    public $id;
    public $name;
    public $description;
    public $shift_duration = 'standard';
    public $start_shift = '07:00';
    public $break_out = '12:00';
    public $break_in = '13:00';
    public $end_shift = '16:00';
    public $work_setup;
    public $isWorkFromHome = false;
    public $min_ot_mins = '120';
    public $max_ot_time = '22:00';
    public $mobile_earliest_clockin = '08:00';
    public $mobile_latest_clockin = '08:00';
    public $web_earliest_clockin = '07:00';
    public $web_latest_clockin = '09:00';

    protected $listeners = ['save'];


    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $records = ShiftSchedule::find($this->id);
    
        if (!$records) {
            return redirect()->route('shift-schedule.index');
        } 

        $this->name = $records->name ?? '';
        $this->description = $records->description ?? '';
        $this->shift_duration = $records->shift_duration ?? '';
        $this->start_shift = $records->start_shift ?? '';
        $this->break_out = $records->break_out ?? '';
        $this->break_in = $records->break_in ?? '';
        $this->end_shift = $records->end_shift ?? '';
        $this->work_setup = $records->work_setup ?? '';
        $this->min_ot_mins = $records->min_ot_mins ?? '';
        $this->max_ot_time = $records->max_ot_time ?? '';
        $this->mobile_earliest_clockin = $records->mobile_earliest_clockin ?? '';
        $this->mobile_latest_clockin = $records->mobile_latest_clockin ?? '';
        $this->web_earliest_clockin = $records->web_earliest_clockin ?? '';
        $this->web_latest_clockin = $records->web_latest_clockin ?? '';

        $this->changeWorkSetup();

    }

    public function rules()
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'shift_duration' => 'required|in:standard,extended,full-day,compressed,part-time',
            'start_shift' => 'required|date_format:H:i',
            'end_shift' => 'required|date_format:H:i',
            'break_in' => 'required|date_format:H:i',
            'break_out' => 'required|date_format:H:i',
            'work_setup' => 'required|in:wfh,onsite',
        ];

        // Add custom validation for shift duration and break times
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                $fromTime = Carbon::createFromFormat('H:i', $this->start_shift);
                $toTime = Carbon::createFromFormat('H:i', $this->end_shift);
                $shiftHours = $fromTime->diffInHours($toTime);

                $expectedHours = match ($this->shift_duration) {
                    'standard' => 9,
                    'extended' => 13,
                    'full-day' => 25,
                    'compressed' => 11,
                    'part-time' => 0,
                    default => null,
                };

                // Validate shift duration
                if ($this->shift_duration !== 'part-time' && $shiftHours !== $expectedHours) {
                    $validator->errors()->add('end_shift', "The shift duration must be exactly {$expectedHours} hours for a {$this->shift_duration} shift.");
                } elseif ($this->shift_duration === 'part-time' && $shiftHours >= 8) {
                    $validator->errors()->add('end_shift', 'The shift duration must be below 8 hours for a part-time shift.');
                }

                // Validate break time duration (must be exactly 1 hour)
                $breakTimeIn = Carbon::createFromFormat('H:i', $this->break_in);
                $breakTimeOut = Carbon::createFromFormat('H:i', $this->break_out);
                $breakDuration = $breakTimeIn->diffInMinutes($breakTimeOut);

                if ($breakDuration !== 60) {
                    $validator->errors()->add('break_out', 'The break time must be exactly 1 hour between break time in and break time out.');
                }

                // Ensure break-in and break-out times are within the shift times
                if (($breakTimeIn->lt($fromTime) || $breakTimeIn->gt($toTime)) || ($breakTimeOut->lt($fromTime) || $breakTimeOut->gt($toTime))) {
                    $validator->errors()->add('break_in', 'The break-in and break-out times must be between the start and end shift times.');
                }
            });
        });

        return $rules;
    }
 
    protected function messages() {
        return [
            'name.required' => 'The name field is required.',
            'description.required' => 'The description field is required.',
            'shift_duration.required' => 'Please select a shift duration.',
            'shift_duration.in' => 'The selected shift duration is invalid. Allowed values are: standard, extended, full-day, compressed, part-time.',
            'start_shift.required' => 'The start time is required.',
            'start_shift.date_format' => 'The start time must be in the format HH:mm.',
            'end_shift.required' => 'The end time is required.',
            'end_shift.date_format' => 'The end time must be in the format HH:mm.',
            'break_in.required' => 'The break time in field is required.',
            'break_in.date_format' => 'The break time in must be in the format HH:mm.',
            'break_out.required' => 'The break time out field is required.',
            'break_out.date_format' => 'The break time out must be in the format HH:mm.',
            'end_shift.valid_shift_duration' => 'The selected shift duration does not match the required hours.',
            'work_setup.required' => 'Please select a work setup.',
            'work_setup.in' => 'The selected work setup is invalid.'
        ];
    }
    
    public function changeWorkSetup() {
        $setup = $this->work_setup;

        if($setup == 'wfh') {
            $this->isWorkFromHome = true;
        } else {
            $this->isWorkFromHome = false;
        }

    }

    public function save(bool $isNotify = true) {
        // Validate basic rules
        $this->validate();

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {

            try {
                // Determine the values for clock-in fields based on work setup
                $mobileEarliestClockin = $this->work_setup === 'wfh' ? $this->mobile_earliest_clockin : null;
                $mobileLatestClockin = $this->work_setup === 'wfh' ? $this->mobile_latest_clockin : null;
                $webEarliestClockin = $this->work_setup === 'wfh' ? $this->web_earliest_clockin : null;
                $webLatestClockin = $this->work_setup === 'wfh' ? $this->web_latest_clockin : null;
            
                // Update or create the shift schedule
                $record = ShiftSchedule::updateOrCreate([
                    'id' => $this->id,
                ], [
                    'name' => $this->name,
                    'description' => $this->description,
                    'shift_duration' => $this->shift_duration,
                    'start_shift' => $this->start_shift,
                    'break_out' => $this->break_out,
                    'break_in' => $this->break_in,
                    'end_shift' => $this->end_shift,
                    'work_setup' => $this->work_setup,
                    'min_ot_mins' => $this->min_ot_mins,
                    'max_ot_time' => $this->max_ot_time,
                    'mobile_earliest_clockin' => $mobileEarliestClockin,
                    'mobile_latest_clockin' => $mobileLatestClockin,
                    'web_earliest_clockin' => $webEarliestClockin,
                    'web_latest_clockin' => $webLatestClockin,
                ]);
            
                // Success message after creating or updating the record
                if (is_null($this->id)) {
                    $this->resetExcept('user_id');
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Shift schedule `' . $record->name . '` has been added.'
                    ]);
                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Shift schedule `' . $record->name . '` has been updated.'
                    ]);
                }
            } catch (\Exception $e) {
                // Error handling
                return $this->dispatch('alert', [
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
        return view('livewire.admin.settings.shift-schedule.create');
    }
}
