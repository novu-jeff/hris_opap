<?php

namespace App\Livewire\Admin\Payroll;

use App\Http\Controllers\Admin\Services\Payroll\SalaryService;
use App\Http\Controllers\Admin\Services\Payroll\ClothingAllowanceService;
use App\Http\Controllers\Admin\Services\Payroll\BonusService;
use App\Http\Controllers\Admin\Services\Payroll\OTService;
use App\Http\Controllers\Admin\Services\PayrollService;
use Illuminate\Support\Facades\Bus;
use App\Models\EmployementTypes;
use App\Models\SalaryPayroll;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Batch;
use Throwable;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $entries = 10;
    public $status = '';
    public $employmentTypes;

    public $dynamicFormFields;
    public $selectedForPayroll;

    public string $type;
    public string $employment_type;
    public string $selectedType;

    public $payroll_date;
    public $cut_off_period;
    public $ot_period;
    public $employment_type_id;
    public array $employeesChecked;
    public string $activeTab;
    public bool $isToCreate = false;

    public string $payroll_id;
    public int $batchProgress = 0;
    public string|null $batchId = null;
    public bool $isBatchProcessing = false;
    public string $batchStatusMessage = 'Please Wait...';
    public $actionBy;

    protected $listeners = ['createPayroll', 'dispatchPayrollJobs', 'cancelPayroll', 'removePayroll'];

    public function mount()
    {
        $this->selectedType = $this->type;
        $this->actionBy = Auth::user();
        $this->employmentTypes = EmployementTypes::all();
        $this->loadDynamicFields();
    }

    public function selectPayroll(string $type) {
        $this->selectedType = $type;
        $this->loadDynamicFields();
        $this->dispatch('showModal', [
            'modal' => 'newPayroll'
        ]);
        $this->dispatch('initDateRange');
    }

    public function loadDynamicFields()
    {
        $options = $this->dynamicFields();

        $subTypes = $options[$this->employment_type]['sub'] ?? [];

        $this->dynamicFormFields = [
            'types' => collect($subTypes)->mapWithKeys(fn($item, $key) => [$key => $item['name']])->toArray(),
            'items' => $subTypes[$this->selectedType] ?? [],
        ];

        if (isset($subTypes[$this->selectedType]['fields']['payroll_date']['value'])) {
            $this->payroll_date = $subTypes[$this->selectedType]['fields']['payroll_date']['value'];
        }
    }


    public function dynamicFields() {
        
        $product = config('app.product');

        if($product == 'government') {
            return [
                'contractual' => [
                    'name' => 'contractual',
                    'sub' => [
                        'salary' => [
                            'name' => 'Salary',
                            'page' => 'salary',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'cut_off_period' => [
                                    'label' => 'Cut Off Period',
                                    'type' => 'text',
                                    'class' => 'range',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                                'payroll_date' => [
                                    'label' => 'Payroll Date',
                                    'type' => 'date',
                                    'rules' => 'required|date'
                                ],
                            ]
                        ],
                        'clothing_allowance' => [
                            'name' => 'Clothing Allowance',
                            'page' => 'clothing_allowance',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'monthyear',
                                    'value' => '',
                                    'rules' =>  'required|date',
                                ],
                            ]
                        ],
                        'mid_year' => [
                            'name' => 'Mid Year Bonus',
                            'page' => 'mid_year',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(5)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                        'year_end' => [
                            'name' => 'Year End Bonus',
                            'page' => 'year_end',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' =>  '',
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(11)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(11)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(12)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                        'ot_pay' => [
                            'name' => 'Overtime Pay',
                            'page' => 'ot_pay',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'ot_period' => [
                                    'label' => 'Overtime Period',
                                    'type' => 'text',
                                    'class' => 'range',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
                'contract of service' => [
                    'name' => 'contract of service',
                    'sub' => [
                        'salary' => [
                            'name' => 'Salary',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contract of Service',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'cut_off_period' => [
                                    'label' => 'Cut Off Period',
                                    'type' => 'text',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                                'payroll_date' => [
                                    'label' => 'Payroll Date',
                                    'type' => 'date',
                                    'rules' => 'required|date'
                                ],
                            ]
                        ],
                        'ot_pay' => [
                            'name' => 'Overtime Pay',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contract of Service',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'ot_period' => [
                                    'label' => 'OT Period (range)',
                                    'type' => 'text',
                                    'class' => 'range',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
                'job order' => [
                    'name' => 'Job Order',
                    'sub' => [
                        'salary' => [
                            'name' => 'Salary',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Job Order',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'cut_off_period' => [
                                    'label' => 'Cut Off Period',
                                    'type' => 'text',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                                'payroll_date' => [
                                    'label' => 'Payroll Date',
                                    'type' => 'date',
                                    'rules' => 'required|date'
                                ],
                            ]
                        ],
                    ]
                ],
            ];
        }

        if($product == 'private') {
            return [
                'rank and file' => [
                    'name' => 'Rank and File',
                    'sub' => [
                        'salary' => [
                            'name' => 'Salary',
                            'page' => 'salary',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'cut_off_period' => [
                                    'label' => 'Cut Off Period',
                                    'type' => 'text',
                                    'class' => 'range',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                                'payroll_date' => [
                                    'label' => 'Payroll Date',
                                    'type' => 'date',
                                    'rules' => 'required|date'
                                ],
                            ]
                        ],
                        'mid_year' => [
                            'name' => 'Mid Year Bonus',
                            'page' => 'mid_year',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(5)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                        'year_end' => [
                            'name' => 'Year End Bonus',
                            'page' => 'year_end',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' =>  '',
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(11)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(11)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(12)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                    ]
                ],
                'manager' => [
                    'name' => 'Manager',
                    'sub' => [
                        'salary' => [
                            'name' => 'Salary',
                            'page' => 'salary',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'cut_off_period' => [
                                    'label' => 'Cut Off Period',
                                    'type' => 'text',
                                    'class' => 'range',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                                'payroll_date' => [
                                    'label' => 'Payroll Date',
                                    'type' => 'date',
                                    'rules' => 'required|date'
                                ],
                            ]
                        ],
                        'mid_year' => [
                            'name' => 'Mid Year Bonus',
                            'page' => 'mid_year',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(5)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                        'year_end' => [
                            'name' => 'Year End Bonus',
                            'page' => 'year_end',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' =>  '',
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(11)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(11)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(12)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                    ]
                ],
                'supervisor' => [
                    'name' => 'Supervisor',
                    'sub' => [
                        'salary' => [
                            'name' => 'Salary',
                            'page' => 'salary',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'cut_off_period' => [
                                    'label' => 'Cut Off Period',
                                    'type' => 'text',
                                    'class' => 'range',
                                    'rules' => [
                                        'required',
                                        'unique:payroll,cut_off_period',
                                        'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
                                    ]
                                ],
                                'payroll_date' => [
                                    'label' => 'Payroll Date',
                                    'type' => 'date',
                                    'rules' => 'required|date'
                                ],
                            ]
                        ],
                        'mid_year' => [
                            'name' => 'Mid Year Bonus',
                            'page' => 'mid_year',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' => ''
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(5)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(5)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                        'year_end' => [
                            'name' => 'Year End Bonus',
                            'page' => 'year_end',
                            'fields' => [
                                'employment_type' => [
                                    'label' => 'Employment Type',
                                    'type' => 'text',
                                    'value' => 'Contractual',
                                    'class' => 'restricted',
                                    'attr' => ['readonly' => true],
                                    'rules' =>  '',
                                ],
                                'payroll_date' => [
                                    'label' => 'Date',
                                    'type' => 'date',
                                    'value' => Carbon::now()->month(11)->format('Y-m-d'),
                                    'rules' =>  'required|date',
                                    'attr' => [
                                        'min' => Carbon::now()->month(11)->day(15)->format('Y-m-d'),
                                        'max' => Carbon::now()->month(12)->day(31)->format('Y-m-d'),
                                    ]
                                ],
                            ]
                        ],
                    ]
                ],
            ];
        }

    }

    protected function rules()
    {
        $options = $this->dynamicFields();

        $fields = $options[$this->employment_type]['sub'][$this->selectedType]['fields'] ?? [];

        $rules = [];

        foreach ($fields as $fieldKey => $fieldConfig) {
            if (isset($fieldConfig['rules'])) {
                $rules[$fieldKey] = $fieldConfig['rules'];
            }
        }

        return $rules;
    }

    public function go_back()
    {
        $this->reset([
            'employeesChecked',
            'isToCreate',
            'activeTab'
        ]);

        $this->resetValidation();
    }

    public function setActiveTab($value) {
        $this->activeTab = $value;
    }

    public function createPayroll()
    {
        $type = $this->type;
        $employmentTypeId = EmployementTypes::where('name', 'like', '%' . $this->employment_type . '%')->value('id');
        $payrollService = app(PayrollService::class);

        if (!$this->isToCreate) {
            if (empty($employmentTypeId)) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Employment type is required to fetch employees.'
                ]); 
            }
            
            $this->employeesChecked = $payrollService->getEmployees($employmentTypeId, $type);
            $this->isToCreate = true;

            return;
        }

        if (empty($this->employeesChecked['eligible'])) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops',
                'message' => 'No employees found for the selected employment type.'
            ]); 
        }

        $map = [
            'salary' => [
                'payroll_date'    => $this->payroll_date,
                'cut_off_period'  => $this->cut_off_period,
                'employment_type' => $employmentTypeId,
            ],
            'clothing_allowance' => [
                'payroll_date'    => $this->payroll_date,
                'employment_type' => $employmentTypeId,
            ],
            'mid_year' => [
                'payroll_date'    => $this->payroll_date,
                'employment_type' => $employmentTypeId,
                'type'            => 'mid_year',
            ],
            'year_end' => [
                'payroll_date'    => $this->payroll_date,
                'employment_type' => $employmentTypeId,
                'type'            => 'year_end',
            ],
            'ot_pay' => [
                'ot_period'       => $this->ot_period,
                'employment_type' => $employmentTypeId,
            ],
        ];

        $process = $payrollService->getProcess($type);
        $data = $map[$type];
        $service = app($process['service']);
        $rules = $service->rules($data);


        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $payroll = $service->createPayroll($data);

        $this->dispatch('start-job-dispatch', [
            'payroll_id'      => $payroll->id,
            'employment_type' => $payroll->employment_type,
            'type'            => $type,
        ]);

        $this->dispatch('hideModal', ['modal' => 'newPayroll']);
    }

    
    public function cancel_payroll(bool $isNotify = true)
    {
        $this->dispatch('cancelPayroll');
        $this->reset([
            'isBatchProcessing',
            'batchStatusMessage',
            'batchProgress',
        ]);
    }

    public function dispatchPayrollJobs(string $payroll_id, string $employmentType, string $type)
    {

        $service = app(PayrollService::class);
        $process = $service->getProcess($type);
        $serviceInstance = app($process['service']);

        $process = $serviceInstance->generateChunks($payroll_id, $employmentType, $type);
        
        $status = $process['status'];

        if($status == 'success') {
            $name = $process['name'];
            $jobs = $process['jobs'];
            $payroll = $process['payroll'];

            $batch = Bus::batch($jobs)
                ->withOption('actionBy', [
                    'id' => $this->actionBy->id,
                    'name' => $this->actionBy->name
                ])
                ->name($name)
                ->catch(function (Batch $batch, Throwable $e) {
                    $this->actionBy?->notify(new Notifications(
                        'error',
                        'An error occurred during processing the payroll.',
                        route('system.jobs', ['id' => $batch->id]),
                        'admin'
                    ));
                })
                ->then(function (Batch $batch) { 
                    $this->actionBy?->notify(new Notifications(
                        'success',
                        'The processing of payroll has been finished.',
                            route('system.jobs', ['id' => $batch->id]),
                        'admin'
                    ));
                })
                ->dispatch();
            
            $payroll->update(['batch_id' => $batch->id]);
            $this->batchId = $batch->id;
            $this->isBatchProcessing = true;

            return;
        }

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Oops',
            'message' => 'No jobs were processed'
        ]); 
    }

    public function checkBatchStatus()
    {
        if (!$this->batchId) return;

        $service = app(PayrollService::class);

        $batch = Bus::findBatch($this->batchId);

        
        if ($batch) {

            $this->batchProgress = $batch->progress();

            $this->batchStatusMessage = match (true) {
                $this->batchProgress < 10   => 'Retrieving employee records...',
                $this->batchProgress < 60   => 'Calculating salaries, deductions, and benefits...',
                $this->batchProgress < 80   => 'Generating payroll items and inserting records...',
                $this->batchProgress < 95   => 'Finalizing reports...',
                $this->batchProgress <= 100 => 'Redirecting...',
            };

            if ($batch->finished()) {
                $model = $service->getProcess($this->type)['models']['parent'];
                $this->dispatch('redirect_to', [
                    'url' => route('payroll.process', ['type' => $this->type, 'payroll_id' => $model::where('batch_id', $this->batchId)->value('id')]),
                    'delay' => 3000
                ]);
            }
        }
    }

    public function deletePayroll($payroll_id) {
        $payroll = SalaryPayroll::find($payroll_id);

        if ($payroll) {
            $batchId = $payroll->batch_id;

            $payroll->delete();

            if ($batchId) {
                Bus::findBatch($batchId)?->delete();
            }

            $this->reset([
                'isBatchProcessing',
                'batchStatusMessage',
                'batchProgress',
            ]);
        }

        return;
    }
    
    public function render()
    {
        return view('livewire.admin.payroll.index');
    }
}
