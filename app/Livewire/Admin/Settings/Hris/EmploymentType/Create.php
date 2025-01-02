<?php

namespace App\Livewire\Admin\Settings\Hris\EmploymentType;

use App\Models\EmployementTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    public function save() {
        
        if (Gate::denies('write employment-type')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {

            EmployementTypes::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Employment Type ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.code' => 'required|unique:employment_types,code',
            'fields.name' => 'required|unique:employment_types,name',
        ];
    }

    public function messages() {
        return [
            'fields.code.required' => 'The employment code is required.',
            'fields.code.unique' => 'The employment code is already taken.',

            'fields.name.required' => 'The employment name is required.',
            'fields.name.unique' => 'The employment name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.employment-type.create');
    }
}
