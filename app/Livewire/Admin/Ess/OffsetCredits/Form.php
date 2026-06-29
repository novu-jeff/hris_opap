<?php

namespace App\Livewire\Admin\Ess\OffsetCredits;

use App\Models\EmployeeInformation;
use App\Models\EmployeeOffsetCredit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public $record_id;
    public $record;

    protected $listeners = [
        'setEmployees',
        'onChange',
    ];

    public $employees = [];

    public $fields = [
        'employee_no' => null,
        'earned_hours' => '',
        'earned_date' => '',
        'source' => 'Manual',
        'reference_no' => '',
        'remarks' => '',
    ];

    public function mount()
    {
       
   

        if ($this->record_id) {

            $this->record = EmployeeOffsetCredit::with('employee.personal')
        ->findOrFail($this->record_id);

            $this->fields = [

                'employee_no' => $this->record->employee_no,
                'earned_hours' => $this->record->earned_hours,
                'earned_date' => \Carbon\Carbon::parse($this->record->earned_date)->format('Y-m-d'),
                'source' => $this->record->source ?? 'Manual',
                'reference_no' => $this->record->reference_no,
                'remarks' => $this->record->remarks,

            ];

            //$this->employees = $this->getEmployees();

           

        } else {

            $this->fields['earned_date'] = now()->toDateString();
            $this->fields['reference_no'] = $this->generateReferenceNo();

        

            $this->employees = EmployeeInformation::with('personal')
            ->where('isDeleted', false)
            ->where('status', 'Active')
            ->orderBy('employee_no')
            ->get();


           

        }

        
    }

    public function setEmployees($employees)
    {
        $this->fields['employee_no'] = $employees ?? [];
    }

    public function setEmployee($employeeNo)
    {
        $this->fields['employee_no'] = $employeeNo;
    }

    public function onChange(array $data) {
        $this->fields['employee_no'] = $data;
        $this->dispatch('set_select');
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

       // dd($this->fields);
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