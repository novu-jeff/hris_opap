<?php

namespace App\Livewire\Admin\Settings\Hris\Department;

use App\Models\CostCenters;
use App\Models\Departments;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    protected function rules() {
        return [
            'fields.name' => 'required|unique:departments,name',
            'fields.code' => 'required',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The department name is required.',
            'fields.name.unique' => 'The department name is already taken.',
    
            'fields.code.required' => 'The department  code is required.',
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            Departments::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Department ' . strtoupper($this->fields['name']) . ' was added successfully.'
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

    public function render()
    {
        return view('livewire.admin.settings.hris.department.create');
    }
}
