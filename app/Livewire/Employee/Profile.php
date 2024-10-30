<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{

    public $employees;
    public array $records;
    public bool $isDualCitizenship = false;
    public array $countries;
    public string $activeTab = 'details';
    public $activeAccordion;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_id;

        $model = EmployeeInformation::class;


        $data = $model::with(
            [
                'personal', 
                'account', 
                'education',
                'parents',
                'children',
                'employment_history'
            ])
            ->where('id', $user_id)
            ->first();
        
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
                'spouse_surname' => $data->parents->spouse_surname ?? null,
                'spouse_firstname' => $data->parents->spouse_firstname ?? null,
                'spouse_middlename' => $data->parents->spouse_middlename ?? null,
                'spouse_suffix' => $data->parents->spouse_suffix ?? null,
                'spouse_occupation' => $data->parents->spouse_occupation ?? null,
                'spouse_business_name_employer' => $data->parents->spouse_business_name_employer ?? null,
                'spouse_business_address' => $data->parents->spouse_business_address ?? null,
                'spouse_contact_no' => $data->parents->spouse_contact_no ?? null,
                'father_surname' => $data->parents->father_surname ?? null,
                'father_firstname' => $data->parents->father_firstname ?? null,
                'father_middlename' => $data->parents->father_middlename ?? null,
                'father_suffix' => $data->parents->suffix ?? null,
                'mother_surname' => $data->parents->mother_surname ?? null,
                'mother_firstname' => $data->parents->mother_firstname ?? null,
                'mother_middlename' => $data->parents->mother_middlename ?? null,
            ],
            'employee_children' => $data->children->isEmpty() ? [] : $data->children->toArray(),
            'employee_employment_history' => $data->employment_history->isEmpty() ? [] : $data->employment_history->toArray(),
        ];
    
        $this->records = $records;
    
        return $this->dispatch('hideModal', [
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

    public function select_change(string $property) {
        
        if($property == 'citizenship') {
            if($this->records['employee_personal']['citizenship'] == 'dual_citizenship') {
                $this->isDualCitizenship = true;
            } else {
                $this->isDualCitizenship = false;
            }
        }
    }

    public function render()
    {
        return view('livewire.employee.profile');
    }
}
