<?php

namespace App\Livewire\Admin\Hris;

use App\Helper\Generate;
use App\Mail\SendEmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeeSchedule;
use App\Models\EmployementTypes;
use App\Models\Positions;
use App\Models\Sections;
use App\Models\ShiftSchedule;
use App\Models\Tranche;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Manual extends Component
{
    public object $sections;
    public object $positions;
    public object $employmentTypes;
    public object $shiftSchedule;
    public object $employeeSchedule;
    public object $salaryGrade;
    public bool $isGovernment = false;
    public array $records = [];

    protected $listeners = ['save'];

    public function mount()
    {
        $this->sections = Sections::all();
        $this->positions = Positions::all();
        $this->employmentTypes = EmployementTypes::all();
        $this->shiftSchedule = ShiftSchedule::all();
        $this->employeeSchedule = EmployeeSchedule::all();

        $this->records = [
            'employee_information' => [
                'employee_no' => '',
                'biometrics_id' => '',
                'shift_schedule' => '',
                'employee_schedule' => '',
                'section_id' => '',
                'position_id' => '',
                'job_completion' => '',
                'step_id' => '1',
                'date_hired' => '',
                'date_resignation' => null,
                'type' => '',
                'status' => 'active',
                'salary_method' => '',
                'monthly_rate' => '',
                'payroll_account_number' => '',
            ],
            'employee_personal' => [
                'profile' => null,
                'firstname' => null,
                'middlename' => null,
                'lastname' => null,
                'suffix' => null,
                'birthday' => null,
                'civil_status' => null,
                'sex' => null,
                'citizenship' => null,
                'citizenship_type' => null,
                'solo_parent' => 'no',
                'country' => null,
                'present_address' => null,
                'present_province' => null,
                'present_city' => null,
                'permanent_address' => null,
                'permanent_province' => null,
                'permanent_city' => null,
                'mobile_number' => null,
                'tel_no' => null,
                'email' => null,
                'height' => null,
                'weight' => null,
                'blood_type' => null,
                'gsis_no' => null,
                'pagibig_no' => null,
                'philhealth_no' => null,
                'sss_no' => null,
                'tin_no' => null,
            ],
            'other_earnings' => [],
            'other_deductions' => [],
            'leaveCredits' => [],
        ];
    }

    public function select_change(string $property) {
        if($property == 'date_hired') {
            $this->records['employee_information']['service_duration'] =
                isset($this->records['employee_information']['date_hired'])
                ? relative_time_duration($this->records['employee_information']['date_hired'])
                : '';

        }
    }

    public function handleSalary()
    {
        $eligible = $this->records['employee_information']['type'] ?? '';
        $position_id = $this->records['employee_information']['position_id'] ?? '';
        $step_id = $this->records['employee_information']['step_id'] ?? '';

        $product = config('app.product');

        if ($product == 'government') {
            $this->isGovernment = true;

            if ($eligible != 3 && $eligible && $position_id && $step_id) {
                $salaryGrade = Positions::where('id', $position_id)->value('salary_grade');
                $stepColumn = 'step_' . $step_id;

                $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn) {
                    $query->where('salary_grade', $salaryGrade)->select('id', 'tranche_id', 'salary_grade', $stepColumn);
                }])->where('eligible', $eligible)->first();

                $salary = $activeTranche?->items->first()?->$stepColumn ?? 0;
                $this->records['employee_information']['monthly_rate'] = $salary;
            } else {
                $this->records['employee_information']['monthly_rate'] = 0;
            }
        } else {
            $this->isGovernment = false;
            $this->positions = Positions::all();
        }
    }

    protected function rules(?string $employee_no = null)
    {
        return [
            'records.employee_information.employee_no' => [
                'required',
                Rule::unique('employee_information', 'employee_no')
            ],
            'records.employee_information.biometrics_id' => [
                'required',
                Rule::unique('employee_information', 'bsd_no')
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

    public function save(bool $isNotify = true)
    {
        if (Gate::denies('write hris')) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'message' => 'You do not have permission to perform this action.',
                'showAlert' => true,
            ]);
        }

        try {
            $this->validate($this->rules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('scrollToError', $e->validator->errors()->keys());
            throw $e;
        }

        if ($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'This action will add a new employee.',
                'action' => 'save'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $employee_no = $this->records['employee_information']['employee_no'];

            $this->employee_information($this->records['employee_information']);

            DB::commit();

            return $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Employee Added!',
                'message' => 'Employee ' . strtoupper($employee_no) . ' has been successfully added.',
                'redirect' => route('hris.show', ['employee_no' => $employee_no, 'form' => 'personal']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'showAlert' => true,
            ]);
        }
    }
    
    public function employee_information(array $data) {
        return EmployeeInformation::create([
            'employee_no' => $data['employee_no'] ?? null,
            'section_id' => $data['section_id'] ?? null,
            'position_id' => $data['position_id'] ?? null,
            'date_hired' => $data['date_hired'] ?? null,
            'bsd_no' => $data['biometrics_id'] ?? null,
            'date_resignation' => $data['date_resignation'] ?? null,
            'employment_type_id' => $data['type'] ?? null,
            'status' => $data['status'] ?? null,
            'salary_method' => $data['salary_method'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? null,
            'payroll_account_number' => $data['payroll_account_number'] ?? null,
        ]);
    }

    public function employee_account(string $employee_no, array $data) {

        $generate = new Generate;

        $applicant_id = $data['applicant_id'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $email_id = $generate->email($employee_no, $firstname, $lastname);

        $user = EmployeeAccount::create([
            'employee_no' => $employee_no,
            'applicant_id' => $applicant_id,
            'email_id' => $email_id,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $user->assignRole('employee');

        $record = EmployeeInformation::with('personal', 'account')->where('employee_no', $employee_no)->first();


        if (!$record || empty($record->account->email)) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Unable to notify this employee, their email address is invalid or empty. Please update it first!'
            ]);
        }

        $data = [
            'is_newly_hired' => false,
            'employee_no' => $record->employee_no,
            'email' => $email_id,
            'fullname' => $record->personal->firstname . ' ' . $record->personal->lastname,
            'password' => $password
        ];

        Mail::to($email)->send(new SendEmployeeAccount($data));

        return;
    }

    public function render()
    {
        return view('livewire.admin.hris.manual');
    }
}
