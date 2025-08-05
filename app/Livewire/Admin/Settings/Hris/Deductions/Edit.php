<?php

namespace App\Livewire\Admin\Settings\Hris\Deductions;

use App\Models\EmployementTypes;
use App\Models\OtherDeductions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{

    public int $id;
    public array $fields;
    public $job_category;

    protected $listeners = ['populateField'];

    public function mount() {
        $this->job_category = EmployementTypes::all();
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id) {
        $records = OtherDeductions::find($id);
        if(!$records) {
            return redirect()->route('other-deductions.index');
        }

        $this->fields = [
            'name' => $records->name,
            'code' => $records->code,
            'amount' => $records->amount,
        ];

    }

    public function populateField($field, $value) {
        if($field == 'eligible') {
            $this->fields['eligible'] = $value;
        }

        $this->dispatch('reinitializeSelect');

    }

    public function onChangeSelect($field, $value) {
        if($field == 'frequency') {
            $this->fields['frequency'] = $value;
        }

        if($field == 'eligible') {
            $this->fields['eligible'] = $value;
        }

        if($field == 'source') {
            $this->fields['source'] = $value;
        }

        $this->dispatch('reinitializeSelect');

    }

    public function rules() {
        return [
            'fields.code' => 'required|string|max:255|exists:other_deductions,code',
            'fields.name' => 'required|string|max:255|exists:other_deductions,name',
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
            
            $deduction = OtherDeductions::updateOrCreate([
                'id' => $this->id
            ],[
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
        
            if ($deduction->wasRecentlyCreated) {
                $this->reset('fields');
            }

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
        return view('livewire.admin.settings.hris.deductions.edit');
    }
}
