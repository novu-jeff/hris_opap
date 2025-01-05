<?php

namespace App\Livewire\Admin\Job\Requirements;

use App\Models\JobRequirements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public $name;

    protected function rules() {
        return [
            'name' => 'required|unique:job_requirements,name',
        ];
    }

    protected function messages() {
        return [
            'name.exists' => 'The name field already exists.'
        ];
    }

    public function save() {

        if (Gate::denies('write requirements')) {
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

            JobRequirements::create([
                'name' => $this->name,
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Requirement for ' . strtoupper($this->name) . ' was added successfully.'
            ]);

            $this->reset();
            
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
        return view('livewire.admin.job.requirements.create');
    }
}
