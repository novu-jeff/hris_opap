<?php

namespace App\Livewire\Admin\Settings\Hris\BankInformation;

use App\Models\BankInformations;
use App\Models\DepartmentCenters;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{

    public int $id;
    public $departments;
    public array $fields;

    public function mount() {
        $this->departments = DepartmentCenters::all();
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id) {

        $records = BankInformations::find($id);

        if(!$records) {
            return redirect()->route('bank-information.index');
        }

        return $this->fields = [
            'name' => $records->name,
            'account_number' => $records->account_number,
            'department' => $records->department_center_id,
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            BankInformations::where('id', $this->id)
                ->update([
                    'name' => $this->fields['name'],
                    'account_number' => $this->fields['account_number'],
                    'department_center_id' => $this->fields['department'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Bank Information for ' . strtoupper($this->fields['name']) . ' was updated successfully.'
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
        return view('livewire.admin.settings.hris.bank-information.edit');
    }
}
