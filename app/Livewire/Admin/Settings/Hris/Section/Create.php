<?php

namespace App\Livewire\Admin\Settings\Hris\Section;

use App\Models\Branches;
use App\Models\Departments;
use App\Models\Sections;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public array $fields;
    public $branches;
    public $departments;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $branches = Branches::all();
        $departments = Departments::all();

        $this->branches = $branches;
        $this->departments = $departments;
    }

    protected function rules() {
        return [
            'fields.name' => 'required|unique:sections,name',
            'fields.code' => 'required',
            'fields.branch' => 'required|exists:branches,id',
            'fields.department' => 'required|exists:departments,id'
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The section name is required.',
            'fields.name.unique' => 'The section name has already been taken.',
    
            'fields.code.required' => 'The section code is required.',
    
            'fields.branch.required' => 'The branch is required.',
            'fields.branch.exists' => 'The selected branch is invalid or does not exist.',
    
            'fields.department.required' => 'The department is required.',
            'fields.department.exists' => 'The selected department is invalid or does not exist.',
        ];
    }
    

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            Sections::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
                'branch_id' => $this->fields['branch'],
                'department_id' => $this->fields['department'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Section ' . strtoupper($this->fields['name']) . ' was added successfully.'
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

    public function render()
    {
        return view('livewire.admin.settings.hris.section.create');
    }
}
