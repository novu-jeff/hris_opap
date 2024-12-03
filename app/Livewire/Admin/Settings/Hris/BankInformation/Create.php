<?php

namespace App\Livewire\Admin\Settings\Hris\BankInformation;

use App\Models\BankInformations;
use App\Models\Departments;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public $departments;
    public array $fields;

    public function mount() {
        $this->departments = Departments::all();
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            BankInformations::create([
                'name' => $this->fields['name'],
                'account_number' => $this->fields['account_number'],
                'department_id' => $this->fields['department'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Bank Information for ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
            'fields.name' => 'required',
            'fields.account_number' => 'required|numeric',
            'fields.department' => 'required',
        ];
    }

    protected function messages() {
        return [
            'fields.name.required' => 'The bank name is required.',
    
            'fields.account_number.required' => 'The account number is required.',
            'fields.account_number.numeric' => 'The account number must be a valid number.',
    
            'fields.department.required' => 'The department is required.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.bank-information.create');
    }
}
