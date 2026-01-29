<?php

namespace App\Livewire\Admin\Settings\Hris\LoanType;

use App\Models\LoanType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{

    public array $fields = [
        'is_salary' => true,
    ];

    public function save() {
        
        /*if (Gate::denies('write employment-type')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }*/

        $this->validate();

        DB::beginTransaction();

        try {

            $employmentType = LoanType::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
            ]);

          /*  DB::table('employment_type_settings')->insert([
                'employment_type_id' => $employmentType->id,
                'is_salary' => true,
                'is_ot_pay' => $this->fields['is_ot_pay'] ?? false,
                'is_clothing_allowance' => $this->fields['is_clothing_allowance'] ?? false,
                'is_mid_year' => $this->fields['is_mid_year'] ?? false,
                'is_year_end' => $this->fields['is_year_end'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);*/

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Loan Type ' . strtoupper($this->fields['name']) . ' was added successfully.'
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

    protected function rules() {
        return [
            'fields.code' => 'required|unique:loan_types,code',
            'fields.name' => 'required|unique:loan_types,name'
        ];
    }

    public function messages() {
        return [
            'fields.code.required' => 'The loan code is required.',
            'fields.code.unique' => 'The loan code is already taken.',

            'fields.name.required' => 'The loan name is required.',
            'fields.name.unique' => 'The loan name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.loan-type.create');
    }
}
