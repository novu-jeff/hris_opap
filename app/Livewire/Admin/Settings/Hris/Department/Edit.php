<?php

namespace App\Livewire\Admin\Settings\Hris\Department;

use App\Models\Departments;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{

    public int $id;
    public $cost_centers;
    public array $fields;

    public function mount() {
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id) {

        $records = Departments::find($id);

        if(!$records) {
            return redirect()->route('department-center.index');
        }

        return $this->fields = [
            'code' => $records->code,
            'name' => $records->name,
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            Departments::where('id', $this->id)
                ->update([
                    'code' => $this->fields['code'],
                    'name' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Department ' . strtoupper($this->fields['name']) . ' was updated successfully.'
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
                Rule::unique('departments', 'name')
                    ->ignore($this->id)
            ],
            'fields.code' => 'required',
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The department name is required.',
            'fields.name.unique' => 'The department name is already taken.',
    
            'fields.code.required' => 'The department code is required.',
    
        ];
    }


    public function render()
    {
        return view('livewire.admin.settings.hris.department.edit');
    }
}
