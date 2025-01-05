<?php

namespace App\Livewire\Admin\Settings\Hris\Position;

use App\Models\Positions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    public function save() {
        
        if (Gate::denies('write positions')) {
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

            Positions::create([
                'name' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Position ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.name' => 'required|unique:positions,name',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The position name is required.',
            'fields.name.unique' => 'The position name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.position.create');
    }
}
