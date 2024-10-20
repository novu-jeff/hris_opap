<?php

namespace App\Livewire\Admin\Settings\Hris\Department;

use App\Models\CostCenters;
use App\Models\DepartmentCenters;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{

    public int $id;
    public $cost_centers;
    public array $fields;

    public function mount() {
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id) {

        $records = DepartmentCenters::find($id);

        if(!$records) {
            return redirect()->route('department-center.index');
        }

        $this->cost_centers = CostCenters::all();

        return $this->fields = [
            'name' => $records->name,
            'cost_center' => $records->cost_center_id,
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            DepartmentCenters::where('id', $this->id)
                ->update([
                    'name' => $this->fields['name'],
                    'cost_center_id' => $this->fields['cost_center'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Department ' . strtoupper($this->fields['name']) . ' was updated successfully.'
            ]);

            
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
            'fields.name' => [
                'required',
                Rule::unique('department_centers', 'name')
                    ->ignore($this->id)
            ],
            'fields.cost_center' => 'required|exists:cost_centers,id',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The cost department name is required.',
            'fields.name.unique' => 'The cost department name is already taken.',
    
            'fields.cost_center.required' => 'The cost center is required.',
            'fields.code.exists' => 'The cost center does not exists.',
    
        ];
    }


    public function render()
    {
        return view('livewire.admin.settings.hris.department.edit');
    }
}
