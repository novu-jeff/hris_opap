<?php

namespace App\Livewire\Admin\Settings\Hris\LoanType;

use App\Models\LoanType;
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
        $records = LoanType::find($id);

       

        $data = $this->fields = [
            'code' => $records->code,
            'name' => $records->name,
            
        ];

        return $data;
    }


    public function save() {
        
        if (Gate::denies('write employment-type')) {
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

            $employmentType = LoanType::find($this->id);
            $employmentType->code = $this->fields['code'];
            $employmentType->name = $this->fields['name'];
            $employmentType->save();

           

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Loan Type ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.code' => [
                'required',
                Rule::unique('loan_types', 'code')
                    ->ignore($this->id)
            ],
            'fields.name' => [
                'required',
                Rule::unique('loan_types', 'name')
                    ->ignore($this->id)
            ],
            
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
        return view('livewire.admin.settings.hris.loan-type.edit');
    }
}
