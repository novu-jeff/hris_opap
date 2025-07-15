<?php

namespace App\Livewire\Admin\Hris\Profile;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Http\Controllers\Admin\Services\OtherServices;
use App\Models\EmployeeInformation;
use App\Models\EmployeeSchedule;
use App\Models\EmployementTypes;
use App\Models\Positions;
use App\Models\Sections;
use App\Models\ShiftSchedule;
use App\Models\Tranche;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Information extends Component
{

    public $form;
    public $employee_no;
    public object $sections;
    public object $positions;
    public object $employmentTypes;
    public object $shiftSchedule;
    public object $employeeSchedule;
    public object $salaryGrade;
    public bool $isGovernment = false;
    public array $records;

    protected $listeners = ['save'];

     public function mount() {
        $this->loadRecords();
        $this->handleSalary();
    }

    public function loadRecords() {

        if (empty($this->employee_no)) {
            return redirect()->route('hris.index');
        }

        $this->sections = Sections::all();
        $this->positions = collect([]);
        $this->employmentTypes = EmployementTypes::all();

        $this->shiftSchedule = ShiftSchedule::all();
        $this->employeeSchedule = EmployeeSchedule::all();

        $otherServices = new OtherServices();
        $earnings = $otherServices->earnings($this->employee_no) ?? [];
        $deductions = $otherServices->deductions($this->employee_no) ?? [];
        $leaveCredits = $otherServices->leaves($this->employee_no) ?? [];

        $data = EmployeeInformation::with([
            'department',
            'personal.gsis_item.gsis',
            'account',
            'education',
            'parents',
            'children',
            'employment_history',
            'civil_service',
            'trainings',
            'others',
            'skills'
        ])->where('employee_no', $this->employee_no)->first();

        if (!$data) {
            $this->records = [];
            return;
        }

        if($data->isTransferingEmp) {
            return redirect()
                ->route('hris.index')
                ->with([
                    'dispatch' => 'isTransfering'
                ]);
        }

        $this->records = [
            'employee_information' => $this->formatInformation($data),
            'employee_personal' => $this->formatPersonal($data),
            'other_earnings' => $earnings,
            'other_deductions' => $deductions,
            'leaveCredits' => $leaveCredits
        ];

        if ($data->section_id) {
            $this->select_change('section');
        }

        if($data->personal->citizenship == 'dual_citizenship') {
            $this->select_change('citizenship');
        }

        if($data->personal->civil_status == 'married') {
            $this->select_change('civil_status');
        }
    }

    public function select_change(string $property) {
        if($property == 'section') {
            $section_id = $this->records['employee_information']['section_id'];
            $record = Sections::with('branch', 'department')->where('id', $section_id)->first();
            
            if($record) {
                $this->records['employee_information']['branch'] = $record->branch->name ?? '';
                $this->records['employee_information']['department'] = $record->department->name ?? '';
            } else {
                $this->records['employee_information']['branch'] = '';
                $this->records['employee_information']['department'] = '';
            }
        }
    }

    public function handleSalary() {
        
        $eligible = $this->records['employee_information']['type'] ?? '';
        $position_id = $this->records['employee_information']['position_id'] ?? '';
        $step_id = $this->records['employee_information']['step_id'] ?? '';

        $product = config('app.product');

        if($product == 'government') {
            $this->isGovernment = true;
            if($eligible != 3) {
                
                if(!empty($eligible)) {
                    $this->positions = Positions::where('type', $eligible)->get();
                }
        
                if (!empty($eligible) && !empty($position_id) && !empty($step_id)) {
        
                    $salaryGrade = Positions::where('id', $position_id)
                        ->value('salary_grade') ?? '';
        
                    $stepColumn = "step_" . ($step_id ?? '');
        
                    $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn, $eligible) {
                            $query->where('salary_grade', $salaryGrade)
                                ->select('id', 'tranche_id', 'salary_grade', $stepColumn);
                        }])
                        ->where('eligible', $eligible)
                        ->first();
                    
                    $salary = ($activeTranche && $activeTranche->items->isNotEmpty()) 
                        ? $activeTranche->items->first()->$stepColumn 
                        : 0;
                
        
                    if ($activeTranche) {
                        $this->records['employee_information']['monthly_rate'] = $salary;
                    }
                } else {
                    $this->records['employee_information']['monthly_rate'] = 0;
                }
            } else {
                $salary = EmployeeInformation::where('employee_no', $this->employee_no)->first();
                if($salary) {
                    $this->records['employee_information']['monthly_rate'] = $salary->monthly_rate;
                } else {
                    $this->records['employee_information']['monthly_rate'] = 0;
                }
            }
        } else {

            $this->positions = Positions::all();
            $this->isGovernment = false;

        }

    }

    public function formatInformation($data) {
        return [
            'id' => $data->id,
            'employee_id' => format_id($data->id, 6),
            'employee_no' => $data->employee_no,
            'biometrics_id' => $data->bsd_no,
            'shift_schedule' => $data->shift_id,
            'employee_schedule' => $data->schedule_id,
            'section_id' => $data->section_id,
            'position_id' => $data->position_id,
            'job_completion' => $data->job_completion,
            'step_id' => $data->step_id ?? '1',
            'date_hired' => $data->date_hired,
            'service_duration' => relative_time_duration($data->date_hired),
            'date_resignation' => $data->date_resignation,
            'type' => $data->employment_type_id,
            'status' => $data->status,
            'salary_method' => $data->salary_method,
            'monthly_rate' => $data->monthly_rate,
            'payroll_account_number' => $data->payroll_account_number,
        ];
    }

    protected function formatPersonal($data) {


        if($data->personal->birth_certificate) {
            $this->hasBirthCert = true;
        } 

        if($data->personal->marriage_certificate) {
            $this->hasMarriageCert = true;
        } 

        return [
            'profile' => $data->personal->profile ?? null,
            'firstname' => $data->personal->firstname ?? null,
            'middlename' => $data->personal->middlename ?? null,
            'lastname' => $data->personal->lastname ?? null,
            'suffix' => $data->personal->suffix ?? null,
            'birthday' => $data->personal->birthday ?? null,
            'civil_status' => $data->personal->civil_status ?? null,
            'sex' => $data->personal->sex ?? null,
            'citizenship' => $data->personal->citizenship ?? null,
            'citizenship_type' => $data->personal->citizenship_type ?? null,
            'solo_parent' => $data->personal->solo_parent ? 'yes' : 'no',
            'country' => $data->personal->country ?? null,
            'present_address' => $data->personal->present_address ?? null,
            'present_province' => $data->personal->present_province ?? null,
            'present_city' => $data->personal->present_city ?? null,
            'permanent_address' => $data->personal->permanent_address ?? null,
            'permanent_province' => $data->personal->permanent_province ?? null,
            'permanent_city' => $data->personal->permanent_city ?? null,
            'mobile_number' => $data->personal->mobile_number ?? null,
            'tel_no' => $data->personal->tel_no ?? null,
            'email' => $data->account->email ?? null,
            'height' => $data->personal->height ?? null,
            'weight' => $data->personal->weight ?? null,
            'blood_type' => $data->personal->blood_type ?? null,
            'gsis_no' => $data->personal->gsis_no ?? null,
            'pagibig_no' => $data->personal->pagibig_no ?? null,
            'philhealth_no' => $data->personal->philhealth_no ?? null,
            'sss_no' => $data->personal->sss_no ?? null,
            'tin_no' => $data->personal->tin_no ?? null,
        ];
    }

    protected function rules(?string $employee_no = null) {
        return [
            'records.employee_information.biometrics_id' => [
                'required',
                Rule::unique('employee_information', 'bsd_no')->ignore($employee_no, 'employee_no')
            ],
            'records.employee_information.status' => 'required|in:active,inactive',
            'records.employee_information.date_hired' => 'required|date',
            'records.employee_information.job_completion' => 'required_if:records.employee_information.type,3|nullable|date',
            

            'records.employee_information.section_id' => 'required|exists:sections,id',
            'records.employee_information.type' => 'required|exists:employment_types,id',
            'records.employee_information.position_id' => 'required_if:records.employee_information.type,1,2|nullable|exists:positions,id|required_without:records.employee_information.type',

            'records.employee_information.step_id' => 'required|in:1,2,3,4,5,6,7,8',
            'records.employee_information.monthly_rate' => 'required|numeric|gt:1000',
            'records.employee_information.salary_method' => 'nullable|in:cash,bank transfer,paycheck,e-wallet',
        ];
    }

    protected function messages() {
        return [
            'records.employee_information.employee_no.required' => 'The employee no is required.',
            'records.employee_information.employee_no.unique' => 'The employee no is already taken.',
            'records.employee_information.biometrics_id.required' => 'The biometrics ID is required.',
            'records.employee_information.biometrics_id.unique' => 'The biometrics ID is already taken.',
            'records.employee_information.type.in' => 'The selected employment type does not exists.',
            'records.employee_information.status.required' => 'The account status is required.',
            'records.employee_information.status.in' => 'The status must be either active or inactive.',
            'records.employee_information.date_hired.required' => 'The date hired is required',
            'records.employee_information.date_hired.date' => 'The date hired must be valid date',
            'records.employee_information.section_id.required' => 'The section is required.',
            'records.employee_information.section_id.exists' => 'The selected section does not exist.',
            'records.employee_information.position_id.required_if' => 'The position field is required when employee type is not job order.',
            'records.employee_information.position_id.exists' => 'The selected position is invalid.',
            'records.employee_information.position_id.required_without' => 'The position is required unless an employee type is provided.',
            'records.employee_information.job_completion.required_if' => 'The job completion date is required when employee type is job order.',
            'records.employee_information.job_completion.date' => 'The job completion must be a valid date.',
            'records.employee_information.step_id.required' => 'The tranche step is required.',
            'records.employee_information.step_id.in' => 'The tranche step is invalid.',
            'records.employee_information.monthly_rate.required' => 'The monthly rate is required',
            'records.employee_information.monthly_rate.numeric' => 'The monthly rate must be numbers',
            'records.employee_information.monthly_rate.gt' => 'The monthly rate must be greather than 1000',
            'records.employee_information.salary_method.in' => 'The salary method must be one of the following: cash, bank transfer, paycheck, or e-wallet.',
            'records.employee_information.type.required' => 'The employment type is required',
            'records.employee_information.type.exists' => 'The selected employment type does not exists.',
        ];
    }

    public function save(bool $isNotify = true) {

        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $id = $this->employee_no;

        try {
            $this->validate($this->rules($id));
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->keys();
            $this->setErrorActiveTabAccordions($errors);
            $this->dispatch('scrollToError', $errors);
            throw $e;
        }

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

            return;
        }

        $record = EmployeeInformation::where('employee_no', $id)->first();
        if (!$record) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: You\'re saving a non-existent employee!'
            ]);
        }

        DB::beginTransaction();

        try {

            $process = new HRISProcessingService;
            $process->save(false, $id, $id, 'information', $this->records);

            DB::commit();

            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee ' . strtoupper($id) . ' records saved successfully.',
                'redirect' => '_stay'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.hris.profile.information');
    }
}
