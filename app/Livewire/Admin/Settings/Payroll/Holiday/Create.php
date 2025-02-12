<?php

namespace App\Livewire\Admin\Settings\Payroll\Holiday;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

    public function save() {
        
        if (Gate::denies('write holidays')) {
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

            $date = Carbon::parse($this->date)->format('m-d');

            Holiday::create([
                'name' => $this->name,
                'date' => $date,
                'type' => $this->type,
                'isYearly' => $this->isYearly,
            ]);

            DB::commit();

            $this->reset();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Holiday was added successfully.'
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
            'type' => 'required|string|in:regular,special-non-working,special-working,company',
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
