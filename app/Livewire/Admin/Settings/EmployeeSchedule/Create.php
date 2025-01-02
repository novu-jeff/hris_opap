<?php

namespace App\Livewire\Admin\Settings\EmployeeSchedule;

use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public $id;
    public $name;
    public $description;
    public $monday = true;
    public $monday_remarks = '';
    public $tuesday = true;
    public $tuesday_remarks = '';
    public $wednesday = true;
    public $wednesday_remarks = '';
    public $thursday = true;
    public $thursday_remarks = '';
    public $friday = true;
    public $friday_remarks = '';
    public $saturday = true;
    public $saturday_remarks = '';
    public $sunday = true;
    public $sunday_remarks = '';

    protected $listeners = ['save'];

    public function onSelect($day)
    {
        if (!$this->$day) {
            $this->{$day . '_remarks'} = ''; 
        }
    }

    protected $rules = [
        'name' => 'required',
        'description' => 'required',
        'monday_remarks' => 'required_if:monday,false|max:255',
        'tuesday_remarks' => 'required_if:tuesday,false|max:255',
        'wednesday_remarks' => 'required_if:wednesday,false|max:255',
        'thursday_remarks' => 'required_if:thursday,false|max:255',
        'friday_remarks' => 'required_if:friday,false|max:255',
        'saturday_remarks' => 'required_if:saturday,false|max:255',
        'sunday_remarks' => 'required_if:sunday,false|max:255',
    ];

    protected function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'description.required' => 'The description field is required.',
            'monday_remarks.required_if' => 'Please provide a remark for Monday if it is unchecked.',
            'monday_remarks.max' => 'Monday remarks should not exceed 255 characters.',
            'tuesday_remarks.required_if' => 'Please provide a remark for Tuesday if it is unchecked.',
            'tuesday_remarks.max' => 'Tuesday remarks should not exceed 255 characters.',
            'wednesday_remarks.required_if' => 'Please provide a remark for Wednesday if it is unchecked.',
            'wednesday_remarks.max' => 'Wednesday remarks should not exceed 255 characters.',
            'thursday_remarks.required_if' => 'Please provide a remark for Thursday if it is unchecked.',
            'thursday_remarks.max' => 'Thursday remarks should not exceed 255 characters.',
            'friday_remarks.required_if' => 'Please provide a remark for Friday if it is unchecked.',
            'friday_remarks.max' => 'Friday remarks should not exceed 255 characters.',
            'saturday_remarks.required_if' => 'Please provide a remark for Saturday if it is unchecked.',
            'saturday_remarks.max' => 'Saturday remarks should not exceed 255 characters.',
            'sunday_remarks.required_if' => 'Please provide a remark for Sunday if it is unchecked.',
            'sunday_remarks.max' => 'Sunday remarks should not exceed 255 characters.',
        ];
    }

    public function save(bool $isNotify = true) {

        if (Gate::denies('write employee-schedule')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

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

            DB::beginTransaction();

            try {
    
                EmployeeSchedule::updateOrCreate([
                    'id' => $this->id,
                ], [
                    'name' => $this->name, 
                    'description' => $this->description,
                    'monday' => $this->monday, 
                    'monday_remarks' => $this->monday_remarks, 
                    'tuesday' => $this->tuesday, 
                    'tuesday_remarks' => $this->tuesday_remarks,
                    'wednesday' => $this->wednesday, 
                    'wednesday_remarks' => $this->wednesday_remarks, 
                    'thursday' => $this->thursday, 
                    'thursday_remarks' => $this->thursday_remarks, 
                    'friday' => $this->friday, 
                    'friday_remarks' => $this->friday_remarks,
                    'saturday' => $this->saturday, 
                    'saturday_remarks' => $this->saturday_remarks, 
                    'sunday' => $this->sunday, 
                    'sunday_remarks' => $this->sunday_remarks
                ]);
    
                DB::commit();
    
                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'showAlert' => true,
                    'message' => 'Employee schedule ' . strtoupper($this->name) . ' was added successfully.'
                ]);
    
                $this->reset();
    
            } catch (\Exception $e) {
    
                DB::rollBack();
    
                $this->dispatch('alert', [
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'showAlert' => true,
                    'message' => 'Error occured: ' . $e->getMessage()
                ]);
            } 
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.employee-schedule.create');
    }
}

