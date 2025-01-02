<?php

namespace App\Livewire\Admin\Settings\Access;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{

    public $id;
    public $name;
    public $description;

    public function mount() {

        $this->loadRecords();

    }

    public function loadRecords() {

        $record = Role::find($this->id);

        if(!$record) {
            return redirect()->route('users-access');
        }

        $this->name = $record->name;
        $this->description = $record->description;

    }

    protected function rules() {
        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')
                    ->ignore($this->id)
            ],
            'description' => 'nullable'
        ];
    }

    protected function message() {
        return [
            'name.required' => 'The name is required.',
            'name.unique' => 'The name field is already taken.'
        ];
    }

    public function save() {

        $this->validate();

        DB::beginTransaction();

        try {
            
            Role::find($this->id)->update([
                'name' => $this->name,
                'guard_name' => 'web',
                'description' => $this->description
            ]);
            
            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Role ' . strtoupper($this->name) . ' was updated successfully.'
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

    public function render()
    {
        return view('livewire.admin.settings.access.create');
    }
}
