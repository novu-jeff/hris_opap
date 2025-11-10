<?php

namespace App\Livewire\Admin\Settings\Hris\Deductions;

use App\Models\OtherDeductions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{
    public $job_category;
    public $fields = [];

    protected $listeners = ['populateField'];

    public function rules() {
        return [
            'fields.code' => 'required|string|max:255',
            'fields.name' => 'required|string|max:255',
            'fields.amount' => 'required|numeric',
        ];
    }

    public function messages() {
        return [
            'fields.code.required' => 'The code field is required.',
            'fields.code.string' => 'The code must be a string.',
            'fields.code.max' => 'The code may not be greater than 255 characters.',

            'fields.name.required' => 'The name field is required.',
            'fields.name.string' => 'The name must be a string.',
            'fields.name.max' => 'The name may not be greater than 255 characters.',

            'fields.amount.required' => 'The amount field is required.',
            'fields.amount.numeric' => 'The amount must be a number.',
        ];
    }

    public function save() {
        if (Gate::denies('write other-earnings')) {
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
            OtherDeductions::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
                'amount' => $this->fields['amount'],
            ]);

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Additional Earning ' . strtoupper($this->fields['code']) . ' was added successfully.'
            ]);
            
            DB::commit();
        
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
        return view('livewire.admin.settings.hris.deductions.create');
    }
}
