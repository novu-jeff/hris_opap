<?php

namespace App\Livewire\Admin\Settings\Hris\CostCenter;

use App\Models\CostCenters;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            CostCenters::create([
                'name' => $this->fields['name'],
                'code' => $this->fields['code'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Cost Center ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.name' => 'required|unique:cost_centers,name',
            'fields.code' => 'required|unique:cost_centers,code',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The cost center name is required.',
            'fields.name.unique' => 'The cost center name is already taken.',
    
            'fields.code.required' => 'The cost center code is required.',
            'fields.code.unique' => 'The cost center code is already taken.',
    
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.cost-center.create');
    }
}
