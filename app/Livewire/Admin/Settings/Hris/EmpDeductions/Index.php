<?php

namespace App\Livewire\Admin\Settings\Hris\EmpDeductions;

use App\Imports\EarningImport;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $id;             
    public $selected_id;   
    public $entries = 10;
    public $search = '';
    public $employees;
    public $page;     
    public $toUpdate;         
    public $file;

    public $fields = [
        'employee_no' => [],    
        'amount_type' => null,  
        'first_term'  => null, 
        'second_term' => null, 
    ];

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['setEmployees', 'remove', 'onChange'];

    public function mount($id)
    {
        $this->id = $id;
    }


    public function setPage(string $page = null, string $toUpdate = null)
    {


        $this->page = $page;
        $this->toUpdate = $toUpdate;

       

        if($page == 'edit' && !is_null($toUpdate)) {
            $data = EmployeeDeductions::where('employee_no', $toUpdate)
                ->where('deduction_id', $this->id)
                ->first();

            $this->fields = [
                'employee_no' => [$toUpdate],    
                'amount' => $data->amount,  
                'valid_until'  => $data->valid_until, 
            ];
        }


        $this->employees = $this->getEmployees();
        $this->dispatch('set_select');
    }

    public function getEmployees()
    {

         return ($this->page === 'create')
        ? EmployeeInformation::with('personal')
            ->whereHas('personal')
            ->whereDoesntHave('deductions', function ($q) {
                $q->where('deduction_id', $this->id);
            })
            ->get()
        : EmployeeInformation::with('personal')
            ->whereHas('personal') // ensures personal exists
            ->get();

    }

    protected function fileRules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ];
    }

    protected function fileMessages(): array
    {
        return [
            'file.required' => 'File is required.',
            'file.mimes'    => 'Only accepts xlsx, xls, and csv.',
        ];
    }

    protected function rules(): array
    {
        return [
            'fields.employee_no'   => 'required|array|min:1',
            'fields.employee_no.*' => 'string|exists:employee_information,employee_no',

            'fields.amount'   => 'required|numeric',
            'fields.valid_until'    => 'nullable|date',
        ];
    }

    protected function messages(): array
    {
        return [
            'fields.employee_no.required'   => 'Please select at least one employee.',
            'fields.employee_no.array'      => 'Employee list must be an array.',
            'fields.employee_no.min'        => 'Please select at least one employee.',
            'fields.employee_no.*.string'   => 'Employee number must be a string.',
            'fields.employee_no.*.exists'   => 'One or more selected employees do not exist.',

            'fields.amount.required'        => 'Amount is required.',
            'fields.amount.numeric'         => 'Amount must be a number.',

            'fields.valid_until.date'       => 'Valid until must be a valid date.',
        ];
    }

    public function setEmployees($employees)
    {
        $this->fields['employee_no'] = $employees ?? [];
    }


    public function upload_file()
    {
        $this->validate($this->fileRules(), $this->fileMessages());

        Excel::import(new EarningImport($this->id), $this->file);

        $this->dispatch('alert', [
            'status'    => 'success',
            'title'     => 'Saved!',
            'showAlert' => true,
            'message'   => 'Earning file imported successfully.',
        ]);
    }

    public function onChange(array $data) {
        $this->fields['employee_no'] = $data;
        $this->dispatch('set_select');
    }

    public function save()
    {
        if (Gate::denies('write employee-deductions')) {
            $this->dispatch('alert', [
                'status'    => 'error',
                'title'     => 'Access Denied!',
                'showAlert' => true,
                'message'   => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->validate($this->rules(), $this->messages());

        DB::beginTransaction();

        try {
            $deductionId   = $this->id;
            $amount  = $this->fields['amount'];
            $validUntil   = $this->fields['valid_until'];
            $employees   = $this->fields['employee_no']; 

            \Log::info('Saving Employee Deductions', [
                'deduction_id' => $deductionId,
                'amount' => $amount,
                'valid_until' => $validUntil,
                'employees' => $employees,
            ]);
            
            foreach ($employees as $empNo) {
                EmployeeDeductions::updateOrCreate(
                     [
                        'deduction_id'  => $deductionId,
                        'employee_no' => $empNo,
                    ],
                    [
                        'amount' => $amount,
                        'valid_until'  => $validUntil,
                    ]
                );
            }

            DB::commit();

            $this->dispatch('set_select');
            $this->dispatch('alert', [
                'status'    => 'success',
                'title'     => 'Saved!',
                'showAlert' => true,
                'message'   => 'Deduction(s) saved.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'status'    => 'error',
                'title'     => 'Error!',
                'showAlert' => true,
                'message'   => $e->getMessage(),
            ]);
        }
    }

    public function remove(bool $isNotify = true, ?string $employee_no = null)
    {

        if ($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'You are about to delete deduction record <b>#' . strtoupper(format_id($employee_no, 6)) . '</b>. This action cannot be undone!';
            $action = 'remove';

            $this->selected_id = $employee_no;

            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            $record = EmployeeDeductions::where('employee_no', $this->selected_id);

            if ($record) {
                $record->delete();
                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Deleted!',
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => '
                    deduction record #' . strtoupper(format_id($this->selected_id, 6)) . ' has been deleted successfully.'
                ]);
            } else {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Error!',
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exist.'
                ]);
            }
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEntries()
    {
        $this->resetPage();
    }

    public function render()
    {
       
        $records = EmployeeDeductions::with('personal')
            ->where('deduction_id', $this->id);

        if ($this->search) {
            $records = $records->where(function ($query) {
                $query->where('employee_no', 'like', '%'.$this->search.'%')
                    ->orWhereHas('personal', function ($q) {
                        $q->where('firstname', 'like', '%'.$this->search.'%')
                          ->orWhere('lastname', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $records = $records->paginate($this->entries);

        return view('livewire.admin.settings.hris.emp-deductions.index', [
            'records' => $records,
        ]);
    }
}
