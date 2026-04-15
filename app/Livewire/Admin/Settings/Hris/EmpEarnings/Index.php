<?php

namespace App\Livewire\Admin\Settings\Hris\EmpEarnings;

use App\Imports\EarningImport;
use App\Models\EmployeeEarnings;
use App\Models\EmployeeInformation;
use App\Models\OtherEarnings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $viewMode;

    public $showModal = false;
    public $mode = 'create'; // or 'edit'

    public $id, $selected_id, $entries = 10, $search = '', $toUpdate, $amountType, $file;
    public $employees;

    public $fields = [
        'employee_no' => [],
        'amount_type' => null,
        'first_term'  => null,
        'second_term' => null,
        'amount' => null,
    ];

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['setEmployees', 'remove'];

    public function mount($id)
    {
        $this->id = $id;
        $config = OtherEarnings::findOrFail($this->id);

        $this->fields['first_term'] = $config->first_term;
        $this->fields['second_term'] = $config->second_term;
        $this->fields['amount'] = $config->amount;

        if ($config->amount_type) {
            $this->onChange('amount_type', $config->amount_type);
        }

        $this->employees = $this->getEmployees();
    }

    public function onChange(string $property, string $value)
    {
        if ($property === 'amount_type') {
            $this->fields['amount_type'] = str_replace('_', ' ', $value);
            $this->amountType = $value;
        }
    }

    public function openModal($mode = 'create', $employee_no = null)
    {
        $this->mode = $mode;
        $this->showModal = true;

        if ($mode === 'create') {
            $this->fields['employee_no'] = [];
        }

        if ($mode === 'edit' && $employee_no) {
            $data = EmployeeEarnings::where('employee_no', $employee_no)
                ->where('earning_id', $this->id)
                ->first();

            $this->fields = [
                'employee_no' => [$employee_no],
                'amount_type' => $data->amount_type ?? null,
                'first_term'  => $data->first_term ?? null,
                'second_term' => $data->second_term ?? null,
                'amount' => $data->amount ?? null,
            ];

            $this->onChange('amount_type', $data->amount_type);
        }

        $this->employees = $this->getEmployees();

        $this->js("
            setTimeout(() => {
                Livewire.dispatch('init-select', {
                    employees: " . json_encode($this->employees) . ",
                    selected: " . json_encode($this->fields['employee_no']) . "
                });
            }, 200);
        ");
    }

    public function closeModal()
    {
        $this->showModal = false;
       // $this->reset($this->fields['amount']);
       $this->fields['amount'] = null;
    }

    public function getEmployees()
    {
        return ($this->mode === 'create')
        ? EmployeeInformation::with('personal')
            ->whereHas('personal')
             ->whereDoesntHave('earnings', function ($q) {
                $q->where('earning_id', $this->id);
            })
            ->get()
        : EmployeeInformation::with('personal')
            ->whereHas('personal') // ensures personal exists
            ->get();
            
    }

    protected function fileRules(): array
    {
        return ['file' => 'required|file|mimes:xlsx,xls,csv'];
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
        $rules = [
            'fields.amount_type' => ['required', Rule::in(['fixed amount', 'percentage', 'basic salary'])],
        ];

        $amountType = $this->fields['amount_type'] ?? null;

        $termRules = ($amountType === 'basic salary') ? 'nullable|numeric' : 'required|numeric';

        
        return [
            'fields.employee_no' => ['required', 'array', 'min:1'], // 🔥 ADD THIS
            'fields.amount' => $termRules,
        ];

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'fields.employee_no.required' => 'Please select at least one employee.',
            'fields.employee_no.min' => 'Please select at least one employee.',
            'fields.amount.required' => 'The amount field is required.',
        ];
    }

    public function setEmployees($employees)
    {
        $this->fields['employee_no'] = $employees ?? [];
    }

    public function save()
    {
        $this->fields['employee_no'] = $this->fields['employee_no'] ?: [];
        if (Gate::denies('write employee-earnings')) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
        }

        $this->validate($this->rules(), $this->messages());

        DB::beginTransaction();

        try {
            $earningId = $this->id;
            $amountType = str_replace(' ', '_', $this->fields['amount_type']);
            $firstTerm = $this->fields['first_term'];
            $secondTerm = $this->fields['second_term'];
            $amount = $this->fields['amount'];

            /*foreach ($this->employees as $employee) {
                $data = [
                    'amount_type' => $amountType,
                ];

                if ($amountType === 'basic_salary') {
                    $data['amount'] = $employee->salary;
                } else {
                    $data['first_term'] = $firstTerm ;
                    $data['second_term'] = $secondTerm;
                }

                EmployeeEarnings::updateOrCreate(
                    [
                        'earning_id' => $earningId,
                        'employee_no' => $employee->employee_no,
                    ],
                    $data
                );
            }*/

             foreach ($this->fields['employee_no'] as $employee_no) {
                // Fetch the employee record
                $employee = EmployeeInformation::where('employee_no', $employee_no)->first();

                if (!$employee) continue; // skip if employee not found

                $data = [
                    'amount_type' => $amountType,
                ];

                if ($amountType === 'basic_salary') {
                    $data['amount'] = $employee->salary;
                } else {
                    $data['amount'] = $amount;
                    $data['first_term'] = $firstTerm;
                    $data['second_term'] = $secondTerm;
                }

                EmployeeEarnings::updateOrCreate(
                    [
                        'earning_id' => $earningId,
                        'employee_no' => $employee_no,
                    ],
                    $data
                );
            }   

            DB::commit();

            // ✅ reset form (optional but recommended)
            //$this->reset('fields');

            // ✅ go back to table
            $this->viewMode = null;

            // ✅ reset pagination to first page
            $this->resetPage();


            $employees = $this->fields['employee_no'];
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Saved!',
                'showAlert' => true,
                'message' => 'Earning(s) saved',
            ]);
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Error!',
                'showAlert' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function remove(bool $isNotify = true, ?string $employee_no = null)
    {
        if ($isNotify) {
            $this->selected_id = $employee_no;
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'You are about to delete earning record <b>#' . strtoupper(format_id($employee_no, 6)) . '</b>. This action cannot be undone!',
                'action' => 'remove',
            ]);
        }

        $record = EmployeeEarnings::where('employee_no', $this->selected_id)->first();

        if ($record) {
            $record->delete();
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Deleted!',
                'id' => $this->selected_id,
                'isRemoveRowDT' => true,
                'message' => 'Earning record #' . strtoupper(format_id($this->selected_id, 6)) . ' has been deleted successfully.'
            ]);
        } else {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Error!',
                'isRemoveRowDT' => false,
                'message' => 'Error: ID does not exist.'
            ]);
        }
    }

    public function render()
    {
        $query = EmployeeEarnings::with('personal')->where('earning_id', $this->id);

        if ($this->search) {
            $this->resetPage();
            $query->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('personal', function ($sub) {
                        $sub->where('firstname', 'like', '%' . $this->search . '%')
                            ->orWhere('lastname', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return view('livewire.admin.settings.hris.emp-earnings.index', [
            'records' => $query->paginate($this->entries),
        ]);
    }
}