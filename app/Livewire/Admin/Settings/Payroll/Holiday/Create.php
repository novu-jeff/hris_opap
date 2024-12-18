<?php

namespace App\Livewire\Admin\Settings\Payroll\Holiday;

use App\Models\Holiday;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $name, $date, $type;

    public $isYearly = true;

    public function checkIfYearly() {
        $type = $this->type;

        if($type == 'company') {
            $this->isYearly = false;
        } else {
            $this->isYearly = true;
        }
    }

    public function resetInputs()
    {
        $this->name = '';
        $this->date = '';
        $this->type = '';

        $this->isYearly = true;
    }

    public function save() {
        
        $this->validate();

        DB::beginTransaction();

        try {

            Holiday::create([
                'name' => $this->name,
                'date' => $this->date,
                'type' => $this->type,
                'isYearly' => $this->isYearly,
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Holiday: ' . strtoupper($this->name) . ' was added successfully.'
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
            'name' => 'required|unique:holidays,name',
            'date' => 'required|date',
            'type' => 'required|string',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'The holiday name is required.',
            'name.unique' => 'The holiday name is already taken.',
            
            'date.required' => 'The date is required.',
            'name.date' => 'Must be a date.',

            'type.required' => 'The type is required.',
            'type.string' => 'Must be a string.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.payroll.holiday.create');
    }
}
