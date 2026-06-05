<?php

namespace App\Livewire\Admin\Settings\Hris\AccomplishmentType;

use App\Models\AccomplishmentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public int $id;

    public array $fields = [];

    public function mount()
    {
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id)
    {
        $record = AccomplishmentType::findOrFail($id);

        $this->fields = [
            'accomplishment_name' => $record->accomplishment_name,
            'is_active' => (string) $record->is_active,
        ];
    }

    public function save()
    {
       // dd($this->fields);
        $this->validate();

        DB::beginTransaction();

        try {

            $record = AccomplishmentType::findOrFail($this->id);

            $record->accomplishment_name = $this->fields['accomplishment_name'];
            $record->is_active = (int) $this->fields['is_active'];
            
           
            
            $record->save();

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => 'Accomplishment Type ' .
                    strtoupper($this->fields['accomplishment_name']) .
                    ' updated successfully.'
            ]);

            return redirect()->route('accomplishment-type.index');

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'showAlert' => true,
                'message' => 'Error occurred: ' . $e->getMessage()
            ]);
        }
    }

    protected function rules()
    {
        return [
            'fields.accomplishment_name' => [
                'required',
                Rule::unique('accomplishment_types', 'accomplishment_name')
                    ->ignore($this->id)
            ],

            'fields.is_active' => [
                'required',
                'boolean'
            ],
        ];
    }

    public function messages()
    {
        return [
            'fields.accomplishment_name.required' =>
                'The accomplishment name is required.',

            'fields.accomplishment_name.unique' =>
                'The accomplishment name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.accomplishment-type.edit');
    }
}