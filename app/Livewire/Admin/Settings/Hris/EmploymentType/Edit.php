<?php

namespace App\Livewire\Admin\Settings\Hris\EmploymentType;

use App\Models\EmployementTypes;
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
        $records = EmployementTypes::find($id);

        if (!$records) {
            return redirect()->route('position.index');
        }

        $settings = DB::table('employment_type_settings')
                    ->where('employment_type_id', $records->id)
                    ->first();

        if (!$settings) {
            $settings = (object) [
                'is_salary' => true,
                'is_ot_pay' => false,
                'is_clothing_allowance' => false,
                'is_mid_year' => false,
                'is_year_end' => false,
                'is_eme_rata' => false,
            ];
        }

        $data = $this->fields = [
            'code' => $records->code,
            'name' => $records->name,
            'is_salary' => (bool) $settings->is_salary,
            'is_ot_pay' => (bool) $settings->is_ot_pay,
            'is_clothing_allowance' => (bool) $settings->is_clothing_allowance,
            'is_mid_year' => (bool) $settings->is_mid_year,
            'is_year_end' => (bool) $settings->is_year_end,
            'is_eme_rata' => (bool) $settings->is_eme_rata,
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

            $employmentType = EmployementTypes::find($this->id);
            $employmentType->code = $this->fields['code'];
            $employmentType->name = $this->fields['name'];
            $employmentType->save();

            $existing = DB::table('employment_type_settings')
                ->where('employment_type_id', $this->id)
                ->first();

            $data = [
                'employment_type_id' => $this->id,
                'is_salary' => $this->fields['is_salary'],
                'is_ot_pay' => $this->fields['is_ot_pay'] ?? false,
                'is_clothing_allowance' => $this->fields['is_clothing_allowance'] ?? false,
                'is_mid_year' => $this->fields['is_mid_year'] ?? false,
                'is_year_end' => $this->fields['is_year_end'] ?? false,
                'is_eme_rata' => $this->fields['is_eme_rata'] ?? false,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('employment_type_settings')
                    ->where('employment_type_id', $this->id)
                    ->update($data);
            } else {
                $data['created_at'] = now();
                DB::table('employment_type_settings')->insert($data);
            }

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Employment Type ' . strtoupper($this->fields['name']) . ' was added successfully.'
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
                Rule::unique('employment_types', 'code')
                    ->ignore($this->id)
            ],
            'fields.name' => [
                'required',
                Rule::unique('employment_types', 'name')
                    ->ignore($this->id)
            ],
            'fields.is_salary' => 'required|boolean',
            'fields.is_ot_pay' => 'nullable|boolean',
            'fields.is_clothing_allowance' => 'nullable|boolean',
            'fields.is_mid_year' => 'nullable|boolean',
            'fields.is_year_end' => 'nullable|boolean',
            'fields.is_eme_rata' => 'nullable|boolean'
        ];
    }

    public function messages() {
        return [
            'fields.code.required' => 'The employment code is required.',
            'fields.code.unique' => 'The employment code is already taken.',

            'fields.name.required' => 'The employment name is required.',
            'fields.name.unique' => 'The employment name is already taken.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.hris.employment-type.edit');
    }
}
