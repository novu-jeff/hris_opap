<?php

namespace App\Livewire\Admin\Settings\Hris\Earnings;

use App\Models\EmployementTypes;
use App\Models\OtherEarnings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{

    public int $id;
    public $job_category;
    public $fields = [];
    public $hasAmount = false;
    public $hasMonthPicked = false;
    public $hasParameter = false;
    public $amountType = '';


    protected $listeners = ['populateField'];

    public function mount() {
    
        $this->job_category = EmployementTypes::all();
        $this->loadRecords($this->id);
    }


    public function loadRecords(int $id) {
        
        $records = OtherEarnings::find($id);

        if(!$records) {
            return redirect()->route('other-earnings.index');
        }

        $this->onChange('amount_type', $records->amount_type);

        $this->fields = [
            'code' => $records->code,
            'name' => $records->name,
            'amount_type' => $records->amount_type,
            'first_term' => $records->first_term,
            'second_term' => $records->second_term,
            'is_taxable' => $records->isTaxable ? 'yes' : 'no',
        ];

    }

    public function populateField($field, $value) {
        if($field == 'eligible') {
            $this->fields['eligible'] = $value;
        }

        if($field == 'frequency') {
            $this->fields['frequency'] = $value;
        }

        $this->dispatch('reinitializeSelect');

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

        $this->dispatch('reinitializeSelect');

        $this->validate();

        DB::beginTransaction();
        
        try {

            $otherEarning = OtherEarnings::find($this->id);

            if (!$otherEarning) {
                throw new \Exception('Record not found.');
            }

            $otherEarning->code = $this->fields['code'];
            $otherEarning->name = $this->fields['name'];
            $otherEarning->amount_type = $this->fields['amount_type'];
            $otherEarning->first_term = $this->fields['first_term'];
            $otherEarning->second_term = $this->fields['second_term'];
            $otherEarning->isTaxable = $this->fields['is_taxable'] == 'yes' ? true : false;

            $otherEarning->save();

            $message = 'Additional Earning ' . strtoupper($this->fields['code']) . ' was updated successfully.';

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => $message,
            ]);

            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'showAlert' => true,
                'message' => 'Error occurred: ' . $e->getMessage(),
            ]);
        }
    }


    public function render()
    {
        return view('livewire.admin.settings.hris.earnings.edit');
    }
}
