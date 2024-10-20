<?php

namespace App\Livewire\Admin\Settings\Hris\Violation;

use App\Models\Violations;
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

        $records = Violations::find($id);

        if(!$records) {
            return redirect()->route('violation.index');
        }

        return $this->fields = [
            'name' => $records->name,
        ];
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            Violations::where('id', $this->id)
                ->update([
                    'name' => $this->fields['name'],
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Violation ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
                Rule::unique('violations', 'name')
                    ->ignore($this->id)
            ],
        ];
    }

    public function messages() {
        return [
            'fields.name.required' => 'The violation name is required.',
            'fields.name.unique' => 'The violation name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.violation.edit');
    }
}
