<?php

namespace App\Livewire\Admin\Settings\Hris\Branch;

use App\Models\Branches;
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

        $records = Branches::find($id);

        if(!$records) {
            return redirect()->route('branch.index');
        }

        return $this->fields = [
            'name' => $records->name,
            'code' => $records->code,
        ];
    }

    public function save() {
        
        if (Gate::denies('write branches')) {
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

            $branch = Branches::find($this->id);
            $branch->name = $this->fields['name'];
            $branch->code = $this->fields['code'];
            $branch->save();

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Branch ' . strtoupper($this->fields['name']) . ' was updated successfully.'
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
                Rule::unique('branches', 'name')
                    ->ignore($this->id)
            ],
            'fields.code' => [
                'required',
                Rule::unique('branches', 'code')
                    ->ignore($this->id)
            ],
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The branch name is required.',
            'fields.name.unique' => 'The branch name is already taken.',
    
            'fields.code.required' => 'The branch code is required.',
            'fields.code.unique' => 'The branch code is already taken.',
    
        ];
    }


    public function render()
    {
        return view('livewire.admin.settings.hris.branch.edit');
    }
}
