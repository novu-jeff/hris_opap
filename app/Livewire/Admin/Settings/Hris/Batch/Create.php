<?php

namespace App\Livewire\Admin\Settings\Hris\Batch;

use App\Models\BatchConfigurations;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            BatchConfigurations::create([
                'name' => $this->fields['name'],
                'batch_id' => $this->fields['batch'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Batch ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.batch' => 'required',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The batch name is required.',
            'fields.name.unique' => 'The batch name is already taken.',
            'fields.batch.unique' => 'The batch id isrequired.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.batch.create');
    }
}
