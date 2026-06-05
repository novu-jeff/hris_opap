<?php

namespace App\Livewire\Admin\Settings\Hris\AccomplishmentType;

use App\Models\AccomplishmentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public array $fields = [
        'is_salary' => true,
    ];

    public function save() {
        
        

        $this->validate();

        DB::beginTransaction();

        try {

            $employmentType = AccomplishmentType::create([
                'accomplishment_name' => $this->fields['accomplishment_name'],
            ]);

          

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Accomplishment Type ' . strtoupper($this->fields['accomplishment_name']) . ' was added successfully.'
            ]);

            $this->reset('fields');

            return redirect()->route('accomplishment-type.index');
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

    protected function rules()
    {
        return [
            'fields.accomplishment_name' => 'required|unique:accomplishment_types,accomplishment_name'
        ];
    }

    public function messages() {
        return [
            'fields.accomplishment_name.required' => 'The accomplishment name is required.',
            'fields.accomplishment_name.unique' => 'The accomplishment name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.accomplishment-type.create');
    }
}
