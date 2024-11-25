<?php

namespace App\Livewire\Admin\Settings\Hris\Deductions;

use App\Models\JobCategory;
use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;
    public $job_category;

    protected $listeners = ['populateField'];

    
    public function mount() {
        $this->job_category = JobCategory::all();
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

        $this->dispatch('reinitializeSelect');

    }

    public function rules() {
        return [
            'fields.name' => 'required|string|max:255',
            'fields.frequency' => 'required|in:bi_monthly,monthly',
            'fields.eligible' => 'required|array|min:1',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The name field is required.',
            'fields.name.string' => 'The name must be a valid string.',
            'fields.name.max' => 'The name should not exceed 255 characters.',
            
            'fields.frequency.required' => 'Please select a frequency.',
            'fields.frequency.in' => 'The frequency must be either "Bi-Monthly" or "Monthly".',
            
            'fields.eligible.required' => 'Please select at least one eligible category.',
            'fields.eligible.array' => 'The eligible field must be an array of selected categories.',
            'fields.eligible.min' => 'You must select at least one eligible category.',
        ];
    }

    public function save() {

        $this->dispatch('reinitializeSelect');

        $this->validate();

        DB::beginTransaction();

        try {

            OtherDeductions::create([
                'code' => $this->fields['code'] ?? null,
                'name' => $this->fields['name'],
                'frequency' => $this->fields['frequency'],
                'eligible' => implode(',', $this->fields['eligible']),
            ]);
            

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Additional Deductions ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
