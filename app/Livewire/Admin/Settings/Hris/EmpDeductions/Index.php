<?php

namespace App\Livewire\Admin\Settings\Hris\EmpDeductions;

use App\Imports\DeductionImport;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeInformation;
use App\Models\OtherDeductions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{

    use WithPagination;
    use WithFileUploads;

    public $id;
    public $entries = 10;
    public $search = '';
    public $deductions = [];
    public $as_of = [];
    public $file;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['remove'];

    public function mount() {
        $this->loadRecords();
    }


    public function loadRecords() {

   
        $employees = EmployeeInformation::with(['personal'])
            ->get();
            
        $deductions = EmployeeDeductions::where('deduction_id', $this->id)->get();

        $this->deductions = [];
        $storedDeductions = [];

        foreach ($employees as $employee) {
            $_deductions = $deductions->firstWhere('employee_no', $employee['employee_no']);
            $storedDeductions[] = $_deductions;
            $this->deductions[$employee['employee_no']]['amount'] = $_deductions ? $_deductions->amount : 0;
            $this->deductions[$employee['employee_no']]['as_of'] = $_deductions ? $_deductions->as_of : 0;
        }

    }

    protected function rules() {
        return [
            'file' => 'required|mimes:xlsx,xls,csv',
        ];
    }

    public function messages() {
        return [
            'file.required' => 'File is required.',
            'file.mimes' => 'Only accepts xlsx, xls, and csv.'
        ];
    }

    public function upload_file(Request $request) {

        $this->validate();

        $value = $this->getValue($this->id);

        Excel::import(new DeductionImport($this->id), $this->file);

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Saved!',
            'showAlert' => true,
            'redirect' => '_reload',
            'message' => 'Deduction added for ' . $value['name'],
        ]);

        return;

    }

    private function getValue(string $type) {

        $data = [
            '1' => [
                'name' => 'DBP Savings',
                'alias' => 'dbp_savings',
            ],
            '2' => [
                'name' => 'Unlad Kawani',
                'alias' => 'unlad_kawani',
            ],
            '3' => [
                'name' => 'Unliquidated Cash Advances',
                'alias' => 'unliquidated_cash_advances',
            ],
            '4' => [
                'name' => 'Philhealth',
                'alias' => 'philhealth',
            ],
            '5' => [
                'name' => 'HDMF',
                'alias' => 'hdmf',
            ],
            '6' => [
                'name' => 'MP2',
                'alias' => 'mp2',
            ],
            '7' => [
                'name' => 'MPLSTLMS',
                'alias' => 'mplstlms',
            ],
            '8' => [
                'name' => 'CIR375, CIR449',
                'alias' => 'cir375_cir449',
            ],
            '9' => [
                'name' => 'ALLOWANCE',
                'alias' => 'allowance',
            ],
        ];

        return $data[$type];

    }

    public function save() {
        
        if (Gate::denies('write employee-deductions')) {
            
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);

            return;
        }


        $this->validate([
            'deductions.*.amount' => 'required|numeric|min:0',
            'deductions.*.as_of' => function ($attribute, $value, $fail) {
                $index = str_replace(['deductions.', '.as_of'], '', $attribute);
                if (isset($this->deductions[$index]['amount']) && $this->deductions[$index]['amount'] > 0 && empty($value)) {
                    $fail('This field is required when amount is greater than 0.');
                }
            },
        ], [
            'deductions.*.amount.required' => '*required',
            'deductions.*.amount.numeric' => '*number only',
        ]);

        DB::beginTransaction();

        try {
                        
            $record = null; 

            foreach ($this->deductions as $employeeNo => $deduction) {
                if ($employeeNo && $deduction !== null) {
                    if ($deduction['amount'] == 0) {
                        EmployeeDeductions::where('employee_no', (string) $employeeNo)
                            ->where('deduction_id', $this->id)
                            ->delete();
                    } else {
                        $record = EmployeeDeductions::firstOrNew([
                            'employee_no' => (string) $employeeNo,
                            'deduction_id' => $this->id,
                        ]);
            
                        $record->amount = $deduction['amount'] ?? 0;
                        $record->as_of = $deduction['as_of'] ?? null; // add null fallback
                        $record->save();
                    }
                }
            }

            DB::commit();

            $deduction = OtherDeductions::find($this->id);

            $action = $record && $record->wasRecentlyCreated ? 'added' : 'updated';

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Saved!',
                'showAlert' => true,
                'message' => 'Deduction for ' . $deduction->name . ' was ' . $action . '.',
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Error!',
                'showAlert' => true,
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function render()
    {

        $model = EmployeeInformation::with(['personal']);

        if ($this->search) {
            $this->resetPage();
        
            $records = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                      ->orWhereHas('personal', function ($q) {
                          $q->where('firstname', 'like', '%' . $this->search . '%')
                            ->orWhere('lastname', 'like', '%' . $this->search . '%');
                      });
            });
        }
        

        $records = $model->paginate($this->entries);

        return view('livewire.admin.settings.hris.emp-deductions.index', [
            'records' => $records
        ]);
    }
}
