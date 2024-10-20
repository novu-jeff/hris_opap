<?php

namespace App\Livewire\Admin\Settings\Hris\EmployeeStatus;

use App\Models\EmployeeStatus;
use App\Models\Positions;
use Illuminate\Support\Facades\DB;
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

        $records = EmployeeStatus::find($id);

        if(!$records) {
            return redirect()->route('position.index');
        }

        return $this->fields = [
            'name' => $records->status,
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            EmployeeStatus::where('id', $this->id)
                ->update([
                    'status' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Employee Status ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
                Rule::unique('employee_statuses', 'status')
                    ->ignore($this->id)
            ],
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The employee status is required.',
            'fields.name.unique' => 'The employee status is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.employee-status.edit');
    }
}
