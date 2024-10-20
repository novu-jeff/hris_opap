<?php

namespace App\Livewire\Admin\Settings\Hris\Branch;

use App\Models\Branches;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            Branches::create([
                'name' => $this->fields['name'],
                'code' => $this->fields['code'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Branch ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.name' => 'required|unique:branches,name',
            'fields.code' => 'required|unique:branches,code',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The branch name is required.',
            'fields.name.unique' => 'The branch name is already taken.',
    
            'fields.code.required' => 'The branch code is required.',
            'fields.code.unique' => 'The branch code is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.branch.create');
    }
}
