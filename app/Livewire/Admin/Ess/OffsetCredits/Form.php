<?php

namespace App\Livewire\Admin\Ess\OffsetCredits;

use App\Models\EmployeeInformation;
use App\Models\EmployeeOffsetCredit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public $record_id;

    public $employees = [];
    protected $listeners = [
        'employee-selected' => 'setEmployee',
    ];

    public array $fields = [

        'employee_no' => '',
        'earned_hours' => '',
        'earned_date' => '',
        'source' => 'Manual',
        'reference_no' => '',
        'remarks' => '',

    ];

    public function mount()
    {
        $this->employees = EmployeeInformation::with('personal')
            ->where('isDeleted', false)
            ->orderBy('employee_no')
            ->get();

        if ($this->record_id) {

            $record = EmployeeOffsetCredit::findOrFail($this->record_id);

            $this->fields = [

                'employee_no' => $record->employee_no,
                'earned_hours' => $record->earned_hours,
                'earned_date' => \Carbon\Carbon::parse($record->earned_date)->format('Y-m-d'),
                'source' => $record->source ?? 'Manual',
                'reference_no' => $record->reference_no,
                'remarks' => $record->remarks,

            ];

        } else {

            $this->fields['earned_date'] = now()->toDateString();
            $this->fields['reference_no'] = $this->generateReferenceNo();

        }

        $this->dispatch('init-select2');

        if ($this->record_id) {
            $this->dispatch('set-selected-employee', [
                'employee_no' => $this->fields['employee_no']
            ]);
        }
    }

    public function setEmployee($data)
    {
        $this->fields['employee_no'] = $data['employee_no'];
    }

    protected function rules()
    {
        return [

            'fields.employee_no' => 'required|exists:employee_information,employee_no',

            'fields.earned_hours' => 'required|numeric|min:0.5',

            'fields.earned_date' => 'required|date',

            'fields.source' => 'required',

            'fields.reference_no' => 'nullable|max:100',

            'fields.remarks' => 'nullable|max:500',

        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->record_id) {

            $credit = EmployeeOffsetCredit::findOrFail($this->record_id);

            $credit->update([

                'employee_no' => $this->fields['employee_no'],

                'earned_hours' => $this->fields['earned_hours'],

                'remaining_hours' => $this->fields['earned_hours'] - $credit->used_hours,

                'earned_date' => $this->fields['earned_date'],

                'source' => $this->fields['source'],

                'reference_no' => $this->fields['reference_no'],

                'remarks' => $this->fields['remarks'],

            ]);

        } else {

            $employee = EmployeeInformation::where('employee_no', $this->fields['employee_no'])->first();

            EmployeeOffsetCredit::create([
                'employee_information_id' => $employee->id,

                'employee_no' => $this->fields['employee_no'],

                'earned_hours' => $this->fields['earned_hours'],

                'used_hours' => 0,

                'remaining_hours' => $this->fields['earned_hours'],

                'earned_date' => $this->fields['earned_date'],

                'source' => $this->fields['source'],

                'reference_no' => $this->fields['reference_no'],

                'remarks' => $this->fields['remarks'],

            ]);

        }

        session()->flash('success','Offset Credit saved successfully.');

        return redirect()->route('ess.offset-credits');
    }

    private function generateReferenceNo()
    {
        return 'OC-' . now()->format('Ymd') . '-' . str_pad(
            EmployeeOffsetCredit::count() + 1,
            5,
            '0',
            STR_PAD_LEFT
        );
    }

    public function render()
    {
        return view('livewire.admin.ess.offset-credits.form');
    }
}