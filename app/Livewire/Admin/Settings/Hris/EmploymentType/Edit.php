<?php

namespace App\Livewire\Admin\Settings\Hris\EmploymentType;

use App\Models\EmployementTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

        $records = EmployementTypes::find($id);

        if(!$records) {
            return redirect()->route('position.index');
        }

        return $this->fields = [
            'code' => $records->code,
            'name' => $records->name,
        ];
    }

    public function save() {
        
        if (Gate::denies('write employment-type')) {
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

            EmployementTypes::where('id', $this->id)
                ->update([
                    'code' => $this->fields['code'],
                    'name' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Employment Type ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.code' => [
                'required',
                Rule::unique('employment_types', 'code')
                    ->ignore($this->id)
            ],
            'fields.name' => [
                'required',
                Rule::unique('employment_types', 'name')
                    ->ignore($this->id)
            ],
        ];
    }

    public function messages() {
        return [
            'fields.code.required' => 'The employment code is required.',
            'fields.code.unique' => 'The employment code is already taken.',

            'fields.name.required' => 'The employment name is required.',
            'fields.name.unique' => 'The employment name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.employment-type.edit');
    }
}
