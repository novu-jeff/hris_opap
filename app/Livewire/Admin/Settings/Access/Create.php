<?php

namespace App\Livewire\Admin\Settings\Access;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{

    public $name;
    public $description;

    protected function rules() {
        return [
            'name' => 'required|unique:roles,name',
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
            
            Role::create([
                'name' => $this->name,
                'guard_name' => 'web',
                'description' => $this->description
            ]);
            
            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Role ' . strtoupper($this->name) . ' was added successfully.'
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
