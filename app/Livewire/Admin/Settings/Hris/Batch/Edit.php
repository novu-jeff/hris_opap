<?php

namespace App\Livewire\Admin\Settings\Hris\Batch;

use App\Models\BatchConfigurations;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{

    public int $id;
    public array $fields;

    public function mount() {
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id) {

        $records = BatchConfigurations::find($id);

        if(!$records) {
            return redirect()->route('batch.index');
        }

        return $this->fields = [
            'name' => $records->name,
            'batch' => $records->batch_id,
        ];
    }

    public function save() {

        $this->validate();

        DB::beginTransaction();

        try {

            BatchConfigurations::where('id', $this->id)
                ->update([
                    'name' => $this->fields['name'],
                    'batch_id' => $this->fields['batch'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Batch ' . strtoupper($this->fields['name']) . ' was updated successfully.'
            ]);

            
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
            'fields.name' => [
                'required',
                Rule::unique('batch_configurations', 'name')
                    ->ignore($this->id)
            ],
            'fields.batch' => 'required' 
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
        return view('livewire.admin.settings.hris.batch.edit');
    }
}
