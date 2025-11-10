<?php

namespace App\Livewire\Admin\Settings\Payroll\Holiday;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public int $id;
    public $name, $date, $type, $isYearly;

    public function mount() {
        $this->loadRecords($this->id);
    }

    public function loadRecords(int $id) {

        $record = Holiday::where('id', $id)->where('isDeleted', false)->first();
        
        if (!$record) {
            return redirect()->route('holiday.index');
        }


        $fullDate = Carbon::parse(Carbon::now()->year . '-' . $record->date)->toDateString();

        $this->name = $record->name;
        $this->date = $fullDate;
        $this->type = $record->type;
        $this->isYearly = $record->isYearly;
    }
    

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

            $holiday = Holiday::find($this->id);
            $holiday->name = $this->name;
            $holiday->date = $date;
            $holiday->type = $this->type;
            $holiday->save();

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Holiday  was updated successfully.'
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
            'name' => [
                'required',
                Rule::unique('holidays', 'name')
                    ->ignore($this->id)
            ],
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
        return view('livewire.admin.settings.payroll.holiday.edit');
    }
}
