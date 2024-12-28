<?php

namespace App\Livewire\Admin\Settings\Hris\EmpDeductions;

use App\Models\EmployeeDeductions;
use App\Models\EmployeeInformation;
use App\Models\OtherDeductions;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public int $id;
    public object $employees;
    public array $fields;
    public int $selected_id;
    public $deduction_id;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    protected $listeners = ['remove'];

    public function mount() {
        $this->loadRecords($this->id);
    }

    public function loadRecords($id) {

        $this->deduction_id = $id; 

        $this->employees = EmployeeInformation::with('personal')->get();
    }

    public function addRecords() {
        $this->dispatch('showModal', [
            'modal' => 'add_new_modal'
        ]);
    }

    public function close_upload() {
        $this->reset('fields');
    }

    public function select_change(string $field) {
        if ($field === 'employee_no') {
            $employeeNo = $this->fields['employee_no'];

            $record = EmployeeDeductions::where('employee_no', $employeeNo)
                ->where('deduction_id', $this->id)
                ->first();

            $this->fields['amount'] = $record ? $record->amount : '';
        }
    }

    public function rules() {
        return [
            'fields.employee_no' => 'required|exists:employee_information,employee_no',
            'fields.amount' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'fields.employee_no.required' => 'The Employee Number is required.',
            'fields.employee_no.exists' => 'The provided Employee Number does not exist in our records.',
            'fields.amount.required' => 'The Amount field is required.',
            'fields.amount.numeric' => 'The Amount must be a numeric value.',
        ];
    }

    public function save() {
        $this->validate();

        DB::beginTransaction();

        try {

            $deduction = OtherDeductions::find($this->id);

            if(!$deduction) {
                return redirect()->route('deductions.index');
            }
            
            $record = EmployeeDeductions::updateOrCreate(
                [
                    'employee_no' => $this->fields['employee_no'],
                    'deduction_id' => $this->id,
                ],
                [
                    'employee_no' => $this->fields['employee_no'],
                    'deduction_id' => $this->id,
                    'amount' => $this->fields['amount'],
                ]
            );

            $action = $record->wasRecentlyCreated ? 'added' : 'updated';

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => $deduction->name .  ' was ' . $action . ' to employee #' . $this->fields['employee_no'],
                'redirect' => route('deductions.index', ['id' => $this->id])
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

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this deduction. Once this action is processed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeDeductions::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $deduction = OtherDeductions::find($this->id);

                if(!$deduction) {
                    return redirect()->route('deductions.index');
                }
                
                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => $deduction->name .  ' was deleted ' . ' to employee #' . $record->employee_no,
                ]);
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!',
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }
    }

    public function render()
    {

        $model =  EmployeeDeductions::with('personal')
            ->where('deduction_id', $this->deduction_id);

        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhere('amount', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function($query) {
                    $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.settings.hris.emp-deductions.index', [
            'records' => $records
        ]);
    }
}
