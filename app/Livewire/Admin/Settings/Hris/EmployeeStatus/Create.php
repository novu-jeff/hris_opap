<?php

namespace App\Livewire\Admin\Settings\Hris\EmployeeStatus;

use App\Models\EmployeeStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            EmployeeStatus::create([
                'status' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Employee Status ' . strtoupper($this->fields['name']) . ' was added successfully.'
            ]);

            $this->reset('fields');
            
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

    protected function rules() {
        return [
            'fields.name' => 'required|unique:employee_statuses,status',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The employee status is required.',
            'fields.name.unique' => 'The employee status is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.employee-status.create');
    }
}
