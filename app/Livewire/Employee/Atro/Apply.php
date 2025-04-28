<?php

namespace App\Livewire\Employee\Atro;

use App\Models\EmployeeAccount;
use App\Models\EmployeeAtro;
use App\Models\EmployeeInformation;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Apply extends Component
{

    public $record_id;
    public $employee_id;
    public $employee_no;
    public $OtherEmployees;
    public array $fields = [
        [
            'date' => '',
            'start_time' => '',
            'end_time' => '',
            'justification' => ''
        ]
    ];

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $employee_no = Auth::user()->employee_no;
        $employee_id = Auth::user()->id;

        $this->employee_id = $employee_id;
        $this->employee_no = $employee_no;

        $this->getOtherEmployees();

        if(!is_null($this->record_id)) {

            $records = EmployeeAtro::where('id', $this->record_id)
                ->where('employee_no', $employee_no)
                ->get();
        
            if(!$records) {
                return redirect()
                    ->route('employee.atro');
            }

            $this->fields = $records->map(function ($record) {
                return [
                    'date' => $record->date,
                    'start_time' => $record->start_time,
                    'end_time' => $record->end_time,
                    'justification' => $record->justification
                ];
            })->toArray(); 

        }

    }

    private function getOtherEmployees() {
        $employees = EmployeeInformation::with('personal')
            ->where('employee_no', '!=', $this->employee_no)
            ->get();

        $this->OtherEmployees = $employees;

        return;
    }

    public function addField() {
        $this->fields[] = [
            'date' => '',
            'start_time' => '',
            'end_time' => '',
            'justification' => ''
        ];
    }

    public function removeField($index) {
        if (count($this->fields) > 1) {
            unset($this->fields[$index]);
            $this->fields = array_values($this->fields); 
        }
    }

    protected function rules() {
        return [
            'fields.*.date' => [
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    if(is_null($this->record_id)) {
                        $employeeNo = $this->employee_no; 
                    
                        $exists = EmployeeAtro::where('employee_no', $employeeNo)
                            ->where('date', $value)
                            ->exists();
    
                        if ($exists) {
                            $fail('An overtime application has already been submitted for this date.');
                        }
                    }
                },
            ],
            'fields.*.start_time' => 'required',
            'fields.*.end_time' => 'required|after:fields.*.start_time',
            'fields.*.justification' => 'required|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'fields.*.date.before' => 'The date must be on previous days.',
            'fields.*.date.required' => 'The date field is required.',
            'fields.*.date.date' => 'The date must be a valid date.',
            'fields.*.start_time.required' => 'The start time field is required.',
            'fields.*.start_time.date_format' => 'The start time must be in the format HH:MM.',
            'fields.*.end_time.required' => 'The end time field is required.',
            'fields.*.end_time.date_format' => 'The end time must be in the format HH:MM.',
            'fields.*.end_time.after' => 'The end time must be after the start time.',
            'fields.*.justification.required' => 'The justification field is required.',
            'fields.*.justification.string' => 'The justification must be a valid string.',
            'fields.*.justification.max' => 'The justification may not exceed 255 characters.',
            'fields.*.date.unique' => 'The employee cannot have multiple records for the same date.',
        ];
    }

    public function save(bool $isNotify = true) {

        $this->validate();
    
        if ($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'Yes, I am sure that all the information I have provided is accurate and true. This ensures that there will be no issues as we proceed.',
                'action' => 'save',
            ]);
        } else {
            try {
                foreach ($this->fields as $field) {
                    EmployeeAtro::updateOrCreate(
                        ['id' => $this->record_id], 
                        [
                            'employee_no' =>  $this->employee_no,
                            'date' => $field['date'],
                            'start_time' => $field['start_time'],
                            'end_time' => $field['end_time'],
                            'justification' => $field['justification'],
                        ]
                    );
                }
    

                if(is_null($this->record_id)) {
                    

                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been submitted. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
                    ]);

                    $user = EmployeeAccount::find($this->employee_id);
                    $message = 'Employee <strong>' . $this->employee_no . '</strong> has submitted an application for <strong>authority to render overtime</strong>.';
                    $redirect = route('ess.atro');
                    $user->notify(new Notifications('info', $message, $redirect, 'admin'));

                    $this->resetExcept('employee_no', 'employee_id');

                    return;

                } else {
                    
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Your application has been updated. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
                    ]);

                }

            } catch (\Exception $e) {
                // Handle errors and dispatch an error message
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Error: ' . $e->getMessage(),
                ]);
            }
        }
    }
    

    public function render()
    {
        return view('livewire.employee.atro.apply');
    }
}
