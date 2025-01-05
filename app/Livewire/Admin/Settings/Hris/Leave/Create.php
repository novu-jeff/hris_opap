<?php

namespace App\Livewire\Admin\Settings\Hris\Leave;

use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public array $fields;

    protected function rules() {
        return [
            'fields.name' => 'required|unique:leave_types,name',
            'fields.code' => 'required',
            'fields.credits' => 'required|numeric',
            'fields.isCummulative' => 'required|in:yes,no'
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The leave name is required.',
            'fields.name.unique' => 'The leave name has already been taken.',
            
            'fields.code.required' => 'The leave code is required.',
            
            'fields.credits.required' => 'The number of credits is required.',
            'fields.credits.numeric' => 'The number of credits must be a numeric value.',
            
            'fields.isCummulative.required' => 'You must specify whether the leave is cumulative.',
            'fields.isCummulative.in' => 'The leave’s cumulative status must be either "yes" or "no".',
            
            'fields.branch.required' => 'The branch is required.',
            'fields.branch.exists' => 'The selected branch is invalid or does not exist.',
            
            'fields.department.required' => 'The department is required.',
            'fields.department.exists' => 'The selected department is invalid or does not exist.',
        ];
    }

    public function save() {
        
        if (Gate::denies('write leave-types')) {
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

            $isCummulative = $this->fields['isCummulative'] == 'yes' ? true : false;

            LeaveType::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
                'credits' => $this->fields['credits'],
                'isCummulative' => $isCummulative,
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Leave type ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
        return view('livewire.admin.settings.hris.leave.create');
    }

}
