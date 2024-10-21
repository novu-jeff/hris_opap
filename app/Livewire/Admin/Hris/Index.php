<?php

namespace App\Livewire\Admin\Hris;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Http\Requests\Admin\Hris\saveRequest;
use App\Models\Branches;
use App\Models\DepartmentCenters;
use App\Models\EmployeeInformation;
use App\Models\Positions;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public $employees;
    public array $records;
    public object $departments;
    public object $branches;
    public object $positions;

    public $activeTab = 'details';
    public $activeAccordion;

    public function loadRecords(int $id = null) {

        $model = EmployeeInformation::class;

        $this->branches = Branches::all();
        $this->departments = DepartmentCenters::all();
        $this->positions = Positions::all();


        if(!is_null($id)) {
            $data = $model::with(
                [
                    'personal', 
                    'account', 
                    'education',
                    'parents',
                    'children',
                    'employment_history'
                ])->first();
        
            // Initialize records array
            $records = [
                'employee_information' => [
                    'id'  => $data->id,
                    'employee_id'  => format_id($data->id, 6),
                    'biometrics_id' => $data->biometrics_id ?? null,
                    'department_id' => $data->department_id ?? null,
                    'branch_id' => $data->branch_id ?? null,
                    'position_id' => $data->position_id ?? null,
                    'date_hired' => $data->date_hired ?? null,
                    'date_resignation' => $data->date_resignation ?? null,
                    'type' => $data->type ?? null,
                    'status' => $data->status ?? null,
                    'salary_method' => $data->salary_method ?? null,
                    'leave_credits' => $data->leave_credits ?? null,
                    'monthly_rate' => $data->monthly_rate ?? null,
                    'payroll_account_number' => $data->payroll_account_number ?? null,
                ],
                'employee_account' => [
                    'email' => $data->account->email ?? null,
                ],
                'employee_personal' => [
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
                    'country' => $data->personal->country ?? null,
                    'present_address' => $data->personal->present_address ?? null,
                    'present_province' => $data->personal->present_province ?? null,
                    'present_city' => $data->personal->present_city ?? null,
                    'permanent_address' => $data->personal->permanent_address ?? null,
                    'permanent_province' => $data->personal->permanent_province ?? null,
                    'permanent_city' => $data->personal->permanent_city ?? null,
                    'mobile_number' => $data->personal->mobile_number ?? null,
                    'tel_no' => $data->personal->tel_no ?? null,
                    'company_email' => $data->personal->company_email ?? null,
                    'height' => $data->personal->height ?? null,
                    'weight' => $data->personal->weight ?? null,
                    'blood_type' => $data->personal->blood_type ?? null,
                    'gsis_no' => $data->personal->gsis_no ?? null,
                    'pagibig_no' => $data->personal->pagibig_no ?? null,
                    'philhealth_no' => $data->personal->philhealth_no ?? null,
                    'sss_no' => $data->personal->sss_no ?? null,
                    'tin_no' => $data->personal->tin_no ?? null,
                ],
                'employee_education' => $data->education->isEmpty() ? [] : $data->education->toArray(),
                'employee_parents' => [
                    'father_surname' => $data->parents->father_surname ?? null,
                    'father_firstname' => $data->parents->father_firstname ?? null,
                    'father_middlename' => $data->parents->father_middlename ?? null,
                    'suffix' => $data->parents->suffix ?? null,
                    'father_occupation' => $data->parents->father_occupation ?? null,
                    'father_business_name' => $data->parents->father_business_name ?? null,
                    'father_business_address' => $data->parents->father_business_address ?? null,
                    'father_tel_no' => $data->parents->father_tel_no ?? null,
                    'mother_surname' => $data->parents->mother_surname ?? null,
                    'mother_firstname' => $data->parents->mother_firstname ?? null,
                    'mother_middlename' => $data->parents->mother_middlename ?? null,
                    'mother_occupation' => $data->parents->mother_occupation ?? null,
                    'mother_business_name' => $data->parents->mother_business_name ?? null,
                    'mother_business_address' => $data->parents->mother_business_address ?? null,
                    'mother_tel_no' => $data->parents->mother_tel_no ?? null
                ],
                'employee_children' => $data->children->isEmpty() ? [] : $data->children->toArray(),
                'employee_employment_history' => $data->employment_history->isEmpty() ? [] : $data->employment_history->toArray(),
            ];
        
            $this->records = $records;
        
            return $this->dispatch('hideModal', [
                'modal' => 'select_employee', 
            ]);

        }

        $this->employees = $model::with('personal')->get();


        return $this->dispatch('showModal', [
            'modal' => 'select_employee', 
        ]);
        


    }
    
    public function setActiveTab($tab) {
        $this->activeTab = $tab;
    }

    public function setActiveAccordion($accordion) {
        if($this->activeAccordion !== $accordion) {
            $this->activeAccordion = $accordion;
        } else {
            $this->activeAccordion = '';
        }
    }

    public function addRecord($tab, $type, $accordion = null) {

        $this->activeAccordion = $accordion;

        $fields = [
            'employee_education' => [
                'level' => '',
                'school_name' => '',
                'course' => '',
                'from_year' => '',
                'to_year' => '',
            ],
            'employee_employment_history' => [
                'position' => '',
                'department' => '',
                'company_name' => '',
                'monthly_salary' => '',
                'employment_status' => '',
                'isGovernment' => '',
                'from_year' => '',
                'to_year' => ''
            ],
            'employee_children' => [
                'firstname' => '',
                'middlename' => '',
                'lastname' => '',
                'birthdate' => '',
            ]
        ];
    
        $this->records[$type][] = $fields[$type];
    }
    
    public function removeRecord($tab, $type, $index) {
        $this->activeTab = $tab;
        
        if (isset($this->records[$type][$index])) {
            unset($this->records[$type][$index]);
            $this->records[$type] = array_values($this->records[$type]);
        }
    }

    protected function rules(int $id) {
        return (new saveRequest())->rules($id);
    }

    public function messages(): array
    {
        return [
            'records.employee_account.email.required' => 'Account email is required.',
            'records.employee_personal.firstname.required' => 'The first name field is required.',
            'records.employee_personal.lastname.required' => 'The last name field is required.', 
        
            'records.employee_children.*.firstname.required' => 'The first name is required.',
            'records.employee_children.*.firstname.string' => 'The first name must be a valid string.',
            'records.employee_children.*.firstname.max' => 'The first name may not exceed 255 characters.',
            
            'records.employee_children.*.middlename.string' => 'The middle name must be a valid string.',
            'records.employee_children.*.middlename.max' => 'The middle name may not exceed 255 characters.',
            
            'records.employee_children.*.lastname.required' => 'The last name is required.',
            'records.employee_children.*.lastname.string' => 'The last name must be a valid string.',
            'records.employee_children.*.lastname.max' => 'The last name may not exceed 255 characters.',
            
            'records.employee_children.*.birthdate.required' => 'The birthdate is required.',
            'records.employee_children.*.birthdate.date' => 'The birthdate must be a valid date.',

            'records.employee_education.*.level.required' => 'The education level is required.',
            'records.employee_education.*.level.string' => 'The education level must be a valid string.',
            
            'records.employee_education.*.school_name.required' => 'The school name is required.',
            'records.employee_education.*.school_name.string' => 'The school name must be a valid string.',
            'records.employee_education.*.school_name.max' => 'The school name may not exceed 255 characters.',
            
            'records.employee_education.*.course.required' => 'The course is required.',
            'records.employee_education.*.course.string' => 'The course must be a valid string.',
            'records.employee_education.*.course.max' => 'The course may not exceed 255 characters.',
            
            'records.employee_education.*.from_year.required' => 'The start year is required.',
            'records.employee_education.*.from_year.date' => 'The start year must be a valid date.',
            
            'records.employee_education.*.to_year.required' => 'The end year is required.',
            'records.employee_education.*.to_year.date' => 'The end year must be a valid date.',
        
            'records.employee_employment_history.*.position.required' => 'The position is required.',
            'records.employee_employment_history.*.position.string' => 'The position must be a valid string.',
            'records.employee_employment_history.*.position.max' => 'The position may not exceed 255 characters.',

            'records.employee_employment_history.*.department.required' => 'The department is required.',
            'records.employee_employment_history.*.department.string' => 'The department must be a valid string.',
            'records.employee_employment_history.*.department.max' => 'The department may not exceed 255 characters.',

            'records.employee_employment_history.*.company_name.required' => 'The company name is required.',
            'records.employee_employment_history.*.company_name.string' => 'The company name must be a valid string.',
            'records.employee_employment_history.*.company_name.max' => 'The company name may not exceed 255 characters.',

            'records.employee_employment_history.*.monthly_salary.required' => 'The monthly salary is required.',
            'records.employee_employment_history.*.monthly_salary.numeric' => 'The monthly salary must be a valid number.',
            'records.employee_employment_history.*.monthly_salary.min' => 'The monthly salary must be at least 0.',

            'records.employee_employment_history.*.employment_status.required' => 'The employment status is required.',
            'records.employee_employment_history.*.employment_status.string' => 'The employment status must be a valid string.',

            'records.employee_employment_history.*.isGovernment.required' => 'The government status is required.',
            'records.employee_employment_history.*.isGovernment.string' => 'The government status must be a valid string.',

            'records.employee_employment_history.*.from_year.required' => 'The start date is required.',
            'records.employee_employment_history.*.from_year.date' => 'The start date must be a valid date.',

            'records.employee_employment_history.*.to_year.required' => 'The end date is required.',
            'records.employee_employment_history.*.to_year.date' => 'The end date must be a valid date.',
            'records.employee_employment_history.*.to_year.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }

    public function save(int $id) {

        $record = EmployeeInformation::find($id);
        if(!$record) {
            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops', 
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: The save ID does not exists.'
            ]);
        }

        $this->validate($this->rules($id));
        
        DB::beginTransaction();

        try {
            $process = new HRISProcessingService;
            $process->save(false, $id, $this->records);
            DB::commit();

            return$this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => false,
                'isReloadDT' => false,
                'message' => 'Employee records updated successfully.' 
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
        return view('livewire.admin.hris.index');
    }
}
