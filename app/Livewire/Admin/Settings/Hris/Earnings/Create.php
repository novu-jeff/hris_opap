<?php

namespace App\Livewire\Admin\Settings\Hris\Earnings;

use App\Models\EmployementTypes;
use App\Models\OtherEarnings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{

    public $job_category;
    public $fields = [];
    public $hasAmount = false;
    public $hasMonthPicked = false;
    public $hasParameter = false;
    public $amountType = '';

    protected $listeners = ['populateField'];

    public function mount() {
        $this->job_category = EmployementTypes::all();
    }

    public function onChange(string $property, string $value) {
        if($property == 'amount_type') {
            $this->amountType = $value;
        }
    }

    public function rules()
    {
        $rules = [
            'fields.code' => 'required|string|max:255',
            'fields.name' => 'required|string|max:255',
            'fields.amount_type' => ['required', Rule::in(['fixed_amount', 'percentage', 'basic_salary'])],
            'fields.is_taxable' => 'required|in:yes,no',
        ];

        if (isset($this->fields['amount_type']) && $this->fields['amount_type'] !== 'basic_salary') {
            $rules['fields.first_term'] = 'required|numeric';
            $rules['fields.second_term'] = 'required|numeric';
        } else {
            $rules['fields.first_term'] = 'nullable|numeric';
            $rules['fields.second_term'] = 'nullable|numeric';
        }

        return $rules;
    }


    public function messages() {
        return [
            'fields.code.required' => 'The code field is required.',
            'fields.code.string' => 'The code must be a string.',
            'fields.code.max' => 'The code may not be greater than 255 characters.',

            'fields.name.required' => 'The name field is required.',
            'fields.name.string' => 'The name must be a string.',
            'fields.name.max' => 'The name may not be greater than 255 characters.',

            'fields.amount_type.required' => 'The amount type field is required.',
            'fields.amount_type.string' => 'The amount type must be a string.',
            'fields.amount_type.max' => 'The amount type may not be greater than 255 characters.',

            'fields.first_term.required' => 'The first term field is required.',

            'fields.second_term.required' => 'The second term field is required.',

            'fields.is_taxable.required' => 'The taxable status field is required.',
            'fields.is_taxable.in' => 'The taxable status must be true or false.',
        ];
    }

    public function save() {

        if (Gate::denies('write other-earnings')) {
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

            OtherEarnings::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
                'amount_type' => $this->fields['amount_type'],
                'first_term' => $this->fields['first_term'] ?? null,
                'second_term' => $this->fields['second_term'] ?? null,
                'isTaxable' => $this->fields['is_taxable'] == 'yes' ? true : false,
            ]);

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Additional Earning ' . strtoupper($this->fields['code']) . ' was added successfully.'
            ]);
            
            DB::commit();
        
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
        return view('livewire.admin.settings.hris.earnings.create');
    }
}
