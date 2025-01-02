<?php

namespace App\Livewire\Admin\Job\Requirements;

use App\Models\JobRequirements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{

    public $id;
    public $name;

    public function mount() {
        $record = JobRequirements::find($this->id);
        $this->name = $record->name;
    }
    
    protected function rules() {
        return [
            'name' => [
                'required',
                Rule::unique('job_requirements', 'name')
                    ->ignore($this->id),
            ],
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

            JobRequirements::where('id', $this->id)
                ->update([
                'name' => $this->name,
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Requirement for ' . strtoupper($this->name) . ' was added successfully.'
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
        return view('livewire.admin.job.requirements.create');
    }
}
