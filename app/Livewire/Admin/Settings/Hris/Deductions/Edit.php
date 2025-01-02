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
            'frequency' => $records->frequency,
            'eligible' => explode(',', $records->eligible), 
            'source' => $records->source
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
            'fields.name' => 'required|string|max:255',
            'fields.frequency' => 'required|in:bi_monthly,monthly',
            'fields.eligible' => 'required|array|min:1',
            'fields.source' => 'required|in:entry,file_upload'
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

        
        if (Gate::denies('write other-deductions')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->dispatch('reinitializeSelect');

        $this->validate();

        DB::beginTransaction();

        
        try {
            OtherDeductions::where('id', $this->id)->update([
                'code' => $this->fields['code'] ?? null,
                'name' => $this->fields['name'],
                'frequency' => $this->fields['frequency'],
                'eligible' => implode(',', $this->fields['eligible']),
                'source' => $this->fields['source']
            ]);

            $message = 'Additional Deductions ' . strtoupper($this->fields['code']) . ' was updated successfully.';

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => $message,
            ]);

            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'showAlert' => true,
                'message' => 'Error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.deductions.edit');
    }
}
