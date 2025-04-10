<?php

namespace App\Livewire\Admin\Settings\Hris\CostCenter;

use App\Models\CostCenters;
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

        $records = CostCenters::find($id);

        if(!$records) {
            return redirect()->route('cost-center.index');
        }

        return $this->fields = [
            'name' => $records->name,
            'code' => $records->code,
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            $costCenter = CostCenters::find($this->id);
            $costCenter->name = $this->fields['name'];
            $costCenter->code = $this->fields['code'];
            $costCenter->save();

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Cost Center ' . strtoupper($this->fields['name']) . ' was updated successfully.'
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
                Rule::unique('cost_centers', 'name')
                    ->ignore($this->id)
            ],
            'fields.code' => [
                'required',
                Rule::unique('cost_centers', 'code')
                    ->ignore($this->id)
            ],
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The cost center name is required.',
            'fields.name.unique' => 'The cost center name is already taken.',
    
            'fields.code.required' => 'The cost center code is required.',
            'fields.code.unique' => 'The cost center code is already taken.',
    
        ];
    }


    public function render()
    {
        return view('livewire.admin.settings.hris.cost-center.edit');
    }
}
