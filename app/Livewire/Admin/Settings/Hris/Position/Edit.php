<?php

namespace App\Livewire\Admin\Settings\Hris\Position;

use App\Models\Positions;
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

        $records = Positions::find($id);

        if(!$records) {
            return redirect()->route('position.index');
        }

        return $this->fields = [
            'name' => $records->name,
            'salary_grade' => $records->salary_grade,
            'type' => $records->type
        ];
    }

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

            Positions::where('id', $this->id)
                ->update([
                    'name' => $this->fields['name'],
                    'salary_grade' => $this->fields['salary_grade']
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Position ' . strtoupper($this->fields['name']) . ' was updated successfully.'
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
                Rule::unique('positions', 'name')
                    ->ignore($this->id),
            ],
            'fields.salary_grade' => 'required|numeric',
            'fields.type' => 'required|exists:employment_types,id'
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The position name is required.',
            'fields.name.unique' => 'The position name is already taken.',
            
            'fields.salary_grade.required' => 'The salary grade is required.',
            'fields.salary_grade.unique' => 'The salary grade is already taken.',

            'fields.type.required' => 'The type is required.',
            'fields.type.unique' => 'The type is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.position.edit');
    }
}
