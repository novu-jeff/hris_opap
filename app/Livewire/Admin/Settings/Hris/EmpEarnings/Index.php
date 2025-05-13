<?php

namespace App\Livewire\Admin\Settings\Hris\EmpEarnings;

use App\Imports\DeductionImport;
use App\Imports\EarningImport;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeEarnings;
use App\Models\EmployeeInformation;
use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
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
    public $earnings = [];
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
            
        $earnings = EmployeeEarnings::where('earning_id', $this->id)->get();

        $this->earnings = [];
        $storedEarnings = [];

        foreach ($employees as $employee) {
            $_earnings = $earnings->firstWhere('employee_no', $employee['employee_no']);
            $storedEarnings[] = $_earnings;
            $this->earnings[$employee['employee_no']]['amount'] = $_earnings ? $_earnings->amount : 0;
            $this->earnings[$employee['employee_no']]['as_of'] = $_earnings ? $_earnings->as_of : 0;
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

        Excel::import(new EarningImport($this->id), $this->file);


        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Saved!',
            'showAlert' => true,
            'message' => 'Earning added for ' . $value['name'],
        ]);

        return;

    }

    private function getValue(string $type) {

        $data = [
            '1' => [
                'name' => 'Personal Economic Relief Allowance',
                'alias' => 'pera',
            ],
            '2' => [
                'name' => 'Clothing Allowance',
                'alias' => 'clothing',
            ],
            '3' => [
                'name' => 'Mid Year Bonus',
                'alias' => 'mid',
            ],
            '4' => [
                'name' => 'Year End Bonus',
                'alias' => 'yearend',
            ],
            '5' => [
                'name' => 'Cash Gift',
                'alias' => 'cashgift',
            ],
            '6' => [
                'name' => 'Premium Pay',
                'alias' => 'premium',
            ],
        ];

        return $data[$type];

    }

    public function save() {
        
        if (Gate::denies('write employee-earnings')) {
            
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);

            return;
        }


        $this->validate([
            'earnings.*.amount' => 'required|numeric|min:0',
            'earnings.*.as_of' => function ($attribute, $value, $fail) {
                $index = str_replace(['earnings.', '.as_of'], '', $attribute);
                if (isset($this->earnings[$index]['amount']) && $this->earnings[$index]['amount'] > 0 && empty($value)) {
                    $fail('This field is required when amount is greater than 0.');
                }
            },
        ], [
            'earnings.*.amount.required' => '*required',
            'earnings.*.amount.numeric' => '*number only',
        ]);

        DB::beginTransaction();

        try {
                        
            $record = null; 

            foreach ($this->earnings as $employeeId => $earnings) {
                if ($employeeId && $earnings !== null) {
                    if ($earnings['amount'] == 0) {
                        EmployeeEarnings::where('employee_no', (string) $employeeId)
                            ->where('earning_id', $this->id)
                            ->delete();
                    } else {
                        $record = EmployeeEarnings::firstOrNew(
                            [
                                'employee_no' => (string) $employeeId,
                                'earning_id' => $this->id,
                            ]
                        );

                        $record->amount = $earnings['amount'] ?? 0;
                        $record->as_of = $earnings['as_of'];
                        $record->save();
                    }
                }
            }

            DB::commit();

            $earning = OtherEarnings::find($this->id);

            $action = $record && $record->wasRecentlyCreated ? 'added' : 'updated';

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Saved!',
                'showAlert' => true,
                'message' => 'Earning for ' . $earning->name . ' was ' . $action . '.',
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

        return view('livewire.admin.settings.hris.emp-earnings.index', [
            'records' => $records
        ]);
    }
}
