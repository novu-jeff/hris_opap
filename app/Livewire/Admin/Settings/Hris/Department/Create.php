<?php

namespace App\Livewire\Admin\Settings\Hris\Department;

use App\Models\CostCenters;
use App\Models\DepartmentCenters;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public $cost_centers;
    public array $fields;

    public function mount()  {
        $this->cost_centers = CostCenters::all();
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            DepartmentCenters::create([
                'name' => $this->fields['name'],
                'cost_center_id' => $this->fields['cost_center'],
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

    protected function rules() {
        return [
            'fields.name' => 'required|unique:department_centers,name',
            'fields.cost_center' => 'required|exists:cost_centers,id',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The cost center name is required.',
            'fields.name.unique' => 'The cost center name is already taken.',
    
            'fields.cost_center.required' => 'The cost center code is required.',
            'fields.code.exists' => 'The cost center does not exists.',

        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.department.create');
    }
}
