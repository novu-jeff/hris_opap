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

    protected $listeners = ['populateField'];

    public function mount() {
        $this->job_category = EmployementTypes::all();
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

    public function onChangeSelect($field, $value) {
        if ($field == 'amount_basis' && ($value == 'entry' || $value == 'percentage')) {
            $this->hasAmount = true;
        } else if ($field == 'amount_basis') {
            $this->hasAmount = false;
        }

        if ($field == 'frequency_basis' && $value == 'month_picked') {
            $this->hasMonthPicked = true;
        } else if ($field == 'frequency_basis') {
            $this->hasMonthPicked = false;
        }

        if($field == 'eligible') {
            $this->fields['eligible'] = $value;
        }

        if($field == 'duration') {
            $this->fields['duration'] = $value;
        }

        if($field == 'context') {
            $this->fields['context'] = $value;
        }

        if($field == 'has_parameter' && $value == 'yes' || $field == 'duration' || $field == 'context') {
            $this->hasParameter = true;
        } else {
            $this->hasParameter = false;
        }

        $this->dispatch('reinitializeSelect');

    }

    public function rules() {
        return [
            'fields.code' => 'required|string|max:255',
            'fields.name' => 'required|string|max:255',
            'fields.amount_basis' => ['required', Rule::in(['entry', 'basic_salary', 'percentage'])],
            'fields.amount' => $this->hasAmount ? 'required|numeric' : 'nullable',
            'fields.frequency_basis' => ['required', Rule::in(['monthly', 'yearly', 'month_picked'])],
            'fields.frequency' => $this->hasMonthPicked ? 'required|array|min:1' : 'nullable|array',
            'fields.eligible' => 'required|array|min:1',
            'fields.is_taxable' => 'required|in:yes,no',
            'fields.is_forecasted' => 'required|in:yes,no',
            'fields.has_parameter' => 'required|in:yes,no',

            'fields.duration' => $this->hasParameter ? 'required|in:days,months,years' : 'nullable|in:days,months,years',
            'fields.count' => $this->hasParameter ? 'required|integer' : 'nullable|integer',
            'fields.context' => $this->hasParameter ? 'required|in:from,prior_to,subsequent_to' : 'nullable|in:from,prior_to,subsequent_to',
            'fields.date' => $this->hasParameter ? 'required|string' : 'nullable|string',
        ];
    }

    public function messages() {
        return [
            'fields.code.required' => 'The code field is mandatory.',
            'fields.code.string' => 'The code must be a string.',
            'fields.code.max' => 'The code should not exceed 255 characters.',
            
            'fields.name.required' => 'The name field is mandatory.',
            'fields.name.string' => 'The name must be a string.',
            'fields.name.max' => 'The name should not exceed 255 characters.',
            
            'fields.amount_basis.required' => 'The amount basis is required.',
            'fields.amount_basis.in' => 'The amount basis must be one of the following: entry, basic_salary, or percentage.',
            
            'fields.amount.required' => 'The amount is required when specified.',
            'fields.amount.numeric' => 'The amount must be a numeric value.',
            
            'fields.frequency_basis.required' => 'The frequency basis is required.',
            'fields.frequency_basis.in' => 'The frequency basis must be one of the following: monthly, yearly, or month_picked.',
            
            'fields.frequency.required' => 'The frequency field is mandatory when month is picked.',
            'fields.frequency.array' => 'The frequency must be an array.',
            'fields.frequency.min' => 'You must select at least one frequency.',
            
            'fields.eligible.required' => 'The eligible categories field is mandatory.',
            'fields.eligible.array' => 'The eligible categories must be an array.',
            'fields.eligible.min' => 'You must select at least one eligible category.',
            
            'fields.is_taxable.required' => 'The taxable status is required.',
            'fields.is_taxable.in' => 'The taxable status must be either "yes" or "no".',
            
            'fields.is_forecasted.required' => 'The forecasted status is required.',
            'fields.is_forecasted.in' => 'The forecasted status must be either "yes" or "no".',
            
            'fields.has_parameter.required' => 'The parameter field is required.',
            'fields.has_parameter.in' => 'The parameter must be either "yes" or "no".',
            
            'fields.duration.required' => 'The duration is required when parameter is set.',
            'fields.duration.in' => 'The duration must be one of the following: days, months, or years.',
            
            'fields.count.required' => 'The count is required when parameter is set.',
            'fields.count.integer' => 'The count must be an integer.',
            
            'fields.context.required' => 'The context field is required when parameter is set.',
            'fields.context.in' => 'The context must be one of the following: from, prior_to, or subsequent_to.',
            
            'fields.date.required' => 'The date is required when parameter is set.',
            'fields.date.string' => 'The date must be a string.',
        ];
    }

    public function save() {

        if (Gate::denies('write other earnings')) {
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


            OtherEarnings::create([
                'code' => $this->fields['code'],
                'name' => $this->fields['name'],
                'amount_basis' => $this->fields['amount_basis'],
                'amount' => $this->fields['amount'] ?? null,
                'frequency_basis' => $this->fields['frequency_basis'],
                'frequency' => !empty($this->fields['frequency']) ? implode(',', $this->fields['frequency']) : null, 
                'eligible' => implode(',', $this->fields['eligible']),
                'isTaxable' => $this->fields['is_taxable'] === 'yes',
                'forcasted' => $this->fields['is_forecasted'] === 'yes' ? 1 : 0, 

                'duration' => $this->fields['duration'] ?? null,
                'count' => $this->fields['count'] ?? null,
                'context' => $this->fields['context'] ?? null, 
                'date' => $this->fields['date'] ?? null

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
