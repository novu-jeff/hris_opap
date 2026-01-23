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

    public bool $isGovernment = false;
    public bool $isEditing = false;

    public array $records = [];

    protected $listeners = ['save'];

    /* =======================
     *  LIFECYCLE
     * ======================= */

    public function mount()
    {
        $this->loadRecords();
    }

    /* =======================
     *  LOAD RECORDS
     * ======================= */

    public function loadRecords()
    {
        $this->isEditing = true;

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
            'personal.gsis_item',
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

        if ($data->isTransferingEmp) {
            return redirect()
                ->route('hris.index')
                ->with(['dispatch' => 'isTransfering']);
        }

        $this->records = [
            'employee_information' => $this->formatInformation($data),
            'employee_personal'    => $this->formatPersonal($data),
            'other_earnings'       => $earnings,
            'other_deductions'     => $deductions,
            'leaveCredits'         => $leaveCredits,
        ];

        if (!empty($data->section_id)) {
            $this->select_change('section');
        }

        /* 🔑 Load salary AFTER records exist */
        $this->handleSalary();

        $this->isEditing = false;
    }

    /* =======================
     *  SELECT CHANGE
     * ======================= */

    public function select_change(string $property)
    {
        if ($property === 'section') {
            $section_id = $this->records['employee_information']['section_id'] ?? null;

            $record = Sections::with('branch', 'department')->find($section_id);

            $this->records['employee_information']['branch'] = $record->branch->name ?? '';
            $this->records['employee_information']['department'] = $record->department->name ?? '';
        }
    }

    /* =======================
     *  SALARY HANDLER
     * ======================= */

    public function handleSalary()
    {
        $info = $this->records['employee_information'] ?? [];

        $eligible    = $info['type'] ?? null;
        $position_id = $info['position_id'] ?? null;
        $step_id     = $info['step_id'] ?? null;

        if (config('app.product') !== 'government') {
            $this->isGovernment = false;
            $this->positions = Positions::all();
            return;
        }

        $this->isGovernment = true;

        /* Load positions by employment type */
        if (!empty($eligible)) {
            $this->positions = Positions::where('type', $eligible)->get();
        }

        /* JOB ORDER → salary from DB */
        if ($eligible == 3) {
            return;
        }

        /* EDIT MODE → do NOT recompute */
        if ($this->isEditing) {
            return;
        }

        /* Require all fields */
        if (!$eligible || !$position_id || !$step_id) {
            $this->records['employee_information']['salary'] = 0;
            return;
        }

        $salaryGrade = Positions::where('id', $position_id)->value('salary_grade');
        $stepColumn  = 'step_' . $step_id;

        $activeTranche = Tranche::with(['items' => function ($query) use ($salaryGrade, $stepColumn) {
                $query->where('salary_grade', $salaryGrade)
                      ->select('id', 'tranche_id', 'salary_grade', $stepColumn);
            }])
            ->where('eligible', $eligible)
            ->where('is_active', 1)
            ->latest('year')
            ->first();

        $salary = ($activeTranche && $activeTranche->items->isNotEmpty())
            ? $activeTranche->items->first()->{$stepColumn}
            : 0;

        $this->records['employee_information']['salary'] = $salary;
    }

    /* =======================
     *  FORMATTERS
     * ======================= */

    protected function formatInformation($data): array
    {
        return [
            'id'                     => $data->id,
            'employee_id'            => format_id($data->id, 6),
            'employee_no'            => $data->employee_no,
            'biometrics_id'          => $data->bsd_no,
            'shift_schedule'         => $data->shift_id,
            'employee_schedule'      => $data->schedule_id,
            'section_id'             => $data->section_id,
            'position_id'            => $data->position_id,
            'job_completion'         => $data->job_completion,
            'step_id'                => $data->step_id,
            'date_hired'             => $data->date_hired,
            'service_duration'       => relative_time_duration($data->date_hired),
            'date_resignation'       => $data->date_resignation,
            'type'                   => $data->employment_type_id,
            'status'                 => $data->status,
            'salary_method'          => $data->salary_method,
            'salary'                 => $data->salary,
            'payroll_account_number' => $data->payroll_account_number,
        ];
    }

    protected function formatPersonal($data): array
    {
        $personal = $data->personal;
        $account  = $data->account;

        return [
            'profile'     => $personal->profile ?? null,
            'firstname'   => $personal->firstname ?? null,
            'middlename'  => $personal->middlename ?? null,
            'lastname'    => $personal->lastname ?? null,
            'suffix'      => $personal->suffix ?? null,
            'birthday'    => $personal->birthday ?? null,
            'civil_status'=> $personal->civil_status ?? null,
            'sex'         => $personal->sex ?? null,
            'citizenship' => $personal->citizenship ?? null,
            'email'       => $account->email ?? null,
        ];
    }

    /* =======================
     *  VALIDATION
     * ======================= */

    protected function rules(?string $employee_no = null): array
    {
        return [
            'records.employee_information.biometrics_id' => [
                'required',
                Rule::unique('employee_information', 'bsd_no')->ignore($employee_no, 'employee_no'),
            ],
            'records.employee_information.status'        => 'required|in:active,inactive',
            'records.employee_information.section_id'    => 'required|exists:sections,id',
            'records.employee_information.type'          => 'required|exists:employment_types,id',
            'records.employee_information.position_id'   => 'required_if:records.employee_information.type,1,2|nullable|exists:positions,id',
            'records.employee_information.step_id'       => 'required|in:1,2,3,4,5,6,7,8',
            'records.employee_information.salary'        => 'required|numeric|gt:1000',
            'records.employee_information.salary_method' => 'nullable|in:cash,land bank atm',
        ];
    }

    /* =======================
     *  SAVE
     * ======================= */

    public function save(bool $isNotify = true)
    {
        if (Gate::denies('write hris')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title'  => 'Access Denied!',
                'message'=> 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $id = $this->employee_no;
        $this->validate($this->rules($id));

        if ($isNotify) {
            $this->dispatch('showConfirmation', [
                'title'   => 'Are you sure?',
                'message' => 'The action cannot be undone.',
                'action'  => 'save',
            ]);
            return;
        }

        DB::transaction(function () use ($id) {
            (new HRISProcessingService)
                ->save(false, $id, $id, 'information', $this->records);
        });

        $this->dispatch('alert', [
            'status'  => 'success',
            'title'   => 'Success!',
            'message' => 'Employee records saved successfully.',
            'redirect'=> '_stay',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.hris.profile.information');
    }
}
