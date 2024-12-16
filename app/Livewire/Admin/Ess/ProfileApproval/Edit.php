<?php

namespace App\Livewire\Admin\Ess\ProfileApproval;

use App\Models\EmployeeChildren;
use App\Models\EmployeeCivilService;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmploymentHistory;
use App\Models\EmployeeOtherWorks;
use App\Models\EmployeeParents;
use App\Models\EmployeePersonal;
use App\Models\EmployeeSkillsHobbies;
use App\Models\EmployeeTrainings;
use App\Models\EmployeeUpdateChildren;
use App\Models\EmployeeUpdateCivilService;
use App\Models\EmployeeUpdateEducation;
use App\Models\EmployeeUpdateEmploymentHistory;
use App\Models\EmployeeUpdateOtherWorks;
use App\Models\EmployeeUpdateParents;
use App\Models\EmployeeUpdatePersonal;
use App\Models\EmployeeUpdateSkillsHobbies;
use App\Models\EmployeeUpdateTrainings;
use Exception;
use Livewire\Component;

class Edit extends Component
{

    public $employee_no;
    public $employees;
    public array $records;
    public array $countries;
    public bool $isFromUpdate = false;

    public $activeTab = 'details';
    public $activeAccordion = 'personal';
    public bool $isDualCitizenship = false;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        // Fetch employee data with relations
        $data = EmployeeUpdatePersonal::with([
            'education', 'parents', 'children', 'employment_history', 
            'civil_service', 'trainings', 'others', 'skills'
        ])->where('employee_no', $this->employee_no)->first();
    
        if (!$data) {
            $this->records = []; // Handle empty state if no data is found
            return;
        }
    
        // Populate employee records
        $this->records = [
            'employee_personal' => $this->formatEmployeePersonal($data),
            'employee_education' => $data->education ? $data->education->toArray() : [],
            'employee_parents' => $this->formatEmployeeParents($data),
            'employee_children' => $data->children ? $data->children->toArray() : [],
            'employee_employment_history' => $data->employment_history ? $data->employment_history->toArray() : [],
            'employee_civil_service' => $data->civil_service ? $data->civil_service->toArray() : [],
            'employee_trainings' => $data->trainings ? $data->trainings->toArray() : [],
            'employee_others' => $data->others ? $data->others->toArray() : [],
            'employee_skills' => $data->skills ? $data->skills->toArray() : [],
        ];
    
        // Handle citizenship logic
        if ($data->citizenship === 'dual_citizenship') {
            $this->select_change('citizenship');
        }
        
    }
    
    protected function formatEmployeePersonal($data) {
        return [
            'profile' => $data->profile ?? null,
            'firstname' => $data->firstname ?? null,
            'middlename' => $data->middlename ?? null,
            'lastname' => $data->lastname ?? null,
            'suffix' => $data->suffix ?? null,
            'birthday' => $data->birthday ?? null,
            'civil_status' => $data->civil_status ?? null,
            'sex' => $data->sex ?? null,
            'citizenship' => $data->citizenship ?? null,
            'citizenship_type' => $data->citizenship_type ?? null,
            'country' => $data->country ?? null,
            'present_address' => $data->present_address ?? null,
            'present_province' => $data->present_province ?? null,
            'present_city' => $data->present_city ?? null,
            'permanent_address' => $data->permanent_address ?? null,
            'permanent_province' => $data->permanent_province ?? null,
            'permanent_city' => $data->permanent_city ?? null,
            'mobile_number' => $data->mobile_number ?? null,
            'tel_no' => $data->tel_no ?? null,
            'email' => $data->email ?? null,
            'height' => $data->height ?? null,
            'weight' => $data->weight ?? null,
            'blood_type' => $data->blood_type ?? null,
            'gsis_no' => $data->gsis_no ?? null,
            'pagibig_no' => $data->pagibig_no ?? null,
            'philhealth_no' => $data->philhealth_no ?? null,
            'sss_no' => $data->sss_no ?? null,
            'tin_no' => $data->tin_no ?? null,
        ];
    }    
    
    protected function formatEmployeeParents($data) {
        $parents = $data->parents;
        return [
            'spouse_surname' => $parents->spouse_surname ?? null,
            'spouse_firstname' => $parents->spouse_firstname ?? null,
            'spouse_middlename' => $parents->spouse_middlename ?? null,
            'spouse_suffix' => $parents->spouse_suffix ?? null,
            'spouse_occupation' => $parents->spouse_occupation ?? null,
            'spouse_business_name_employer' => $parents->spouse_business_name_employer ?? null,
            'spouse_business_address' => $parents->spouse_business_address ?? null,
            'spouse_contact_no' => $parents->spouse_contact_no ?? null,
            'father_surname' => $parents->father_surname ?? null,
            'father_firstname' => $parents->father_firstname ?? null,
            'father_middlename' => $parents->father_middlename ?? null,
            'father_suffix' => $parents->suffix ?? null,
            'mother_surname' => $parents->mother_surname ?? null,
            'mother_firstname' => $parents->mother_firstname ?? null,
            'mother_middlename' => $parents->mother_middlename ?? null,
        ];
    }

    public function setActiveTab($tab) {
        $this->activeTab = $tab;
    }

    public function setActiveAccordion($accordion) {
        $this->activeAccordion = $accordion;
    }

    public function approve(bool $isNotify = true) {
        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {

            try {

                $employee_update = EmployeeUpdatePersonal::where('employee_no', $this->employee_no);


            } catch (\Exception $e) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function rejected() {

    }

    public function updateProfileData($employee_no) {
        $employeePersonal = EmployeePersonal::where('employee_no', $employee_no)->first();
        $employeeUpdatePersonal = EmployeeUpdatePersonal::where('employee_no', $employee_no)->first();

        if ($employeePersonal && $employeeUpdatePersonal) {
            // Update fields from the update table to the personal table, only if they are present in update data
            $employeePersonal->update([
                'profile' => $employeeUpdatePersonal->profile ?? $employeePersonal->profile,
                'firstname' => $employeeUpdatePersonal->firstname ?? $employeePersonal->firstname,
                'middlename' => $employeeUpdatePersonal->middlename ?? $employeePersonal->middlename,
                'lastname' => $employeeUpdatePersonal->lastname ?? $employeePersonal->lastname,
                'suffix' => $employeeUpdatePersonal->suffix ?? $employeePersonal->suffix,
                'birthday' => $employeeUpdatePersonal->birthday ?? $employeePersonal->birthday,
                'age' => $employeeUpdatePersonal->age ?? $employeePersonal->age,
                'civil_status' => $employeeUpdatePersonal->civil_status ?? $employeePersonal->civil_status,
                'sex' => $employeeUpdatePersonal->sex ?? $employeePersonal->sex,
                'citizenship' => $employeeUpdatePersonal->citizenship ?? $employeePersonal->citizenship,
                'citizenship_type' => $employeeUpdatePersonal->citizenship_type ?? $employeePersonal->citizenship_type,
                'country' => $employeeUpdatePersonal->country ?? $employeePersonal->country,
                'present_address' => $employeeUpdatePersonal->present_address ?? $employeePersonal->present_address,
                'present_province' => $employeeUpdatePersonal->present_province ?? $employeePersonal->present_province,
                'present_city' => $employeeUpdatePersonal->present_city ?? $employeePersonal->present_city,
                'permanent_address' => $employeeUpdatePersonal->permanent_address ?? $employeePersonal->permanent_address,
                'permanent_province' => $employeeUpdatePersonal->permanent_province ?? $employeePersonal->permanent_province,
                'permanent_city' => $employeeUpdatePersonal->permanent_city ?? $employeePersonal->permanent_city,
                'mobile_number' => $employeeUpdatePersonal->mobile_number ?? $employeePersonal->mobile_number,
                'tel_no' => $employeeUpdatePersonal->tel_no ?? $employeePersonal->tel_no,
                'email' => $employeeUpdatePersonal->email ?? $employeePersonal->email,
                'height' => $employeeUpdatePersonal->height ?? $employeePersonal->height,
                'weight' => $employeeUpdatePersonal->weight ?? $employeePersonal->weight,
                'blood_type' => $employeeUpdatePersonal->blood_type ?? $employeePersonal->blood_type,
                'gsis_no' => $employeeUpdatePersonal->gsis_no ?? $employeePersonal->gsis_no,
                'pagibig_no' => $employeeUpdatePersonal->pagibig_no ?? $employeePersonal->pagibig_no,
                'philhealth_no' => $employeeUpdatePersonal->philhealth_no ?? $employeePersonal->philhealth_no,
                'sss_no' => $employeeUpdatePersonal->sss_no ?? $employeePersonal->sss_no,
                'tin_no' => $employeeUpdatePersonal->tin_no ?? $employeePersonal->tin_no,
            ]);
        }    

    }

    public function updateEducationData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateEducation::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeEducation::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'level' => $employeeUpdatePersonal->level ?? null,
                    'school_name' => $employeeUpdatePersonal->school_name ?? null,
                    'course' => $employeeUpdatePersonal->course ?? null,
                    'from_year' => $employeeUpdatePersonal->from_year ?? null,
                    'to_year' => $employeeUpdatePersonal->to_year ?? null,
                ]
            );
    
        } 
    }

    public function updateParentsData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateParents::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeParents::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'spouse_surname' => $employeeUpdatePersonal->spouse_surname ?? null,
                    'spouse_firstname' => $employeeUpdatePersonal->spouse_firstname ?? null,
                    'spouse_middlename' => $employeeUpdatePersonal->spouse_middlename ?? null,
                    'spouse_suffix' => $employeeUpdatePersonal->spouse_suffix ?? null,
                    'father_surname' => $employeeUpdatePersonal->father_surname ?? null,
                    'father_firstname' => $employeeUpdatePersonal->father_firstname ?? null,
                    'father_middlename' => $employeeUpdatePersonal->father_middlename ?? null,
                    'father_suffix' => $employeeUpdatePersonal->father_suffix ?? null,
                    'father_occupation' => $employeeUpdatePersonal->father_occupation ?? null,
                    'father_business_name' => $employeeUpdatePersonal->father_business_name ?? null,
                    'father_business_address' => $employeeUpdatePersonal->father_business_address ?? null,
                    'father_tel_no' => $employeeUpdatePersonal->father_tel_no ?? null,
                    'mother_surname' => $employeeUpdatePersonal->mother_surname ?? null,
                    'mother_firstname' => $employeeUpdatePersonal->mother_firstname ?? null,
                    'mother_middlename' => $employeeUpdatePersonal->mother_middlename ?? null,
                    'mother_occupation' => $employeeUpdatePersonal->mother_occupation ?? null,
                    'mother_business_name' => $employeeUpdatePersonal->mother_business_name ?? null,
                    'mother_business_address' => $employeeUpdatePersonal->mother_business_address ?? null,
                    'mother_tel_no' => $employeeUpdatePersonal->mother_tel_no ?? null,
                ]
            );
    
        } 
    }

    public function updateChildrenData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateChildren::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeChildren::updateOrCreate(
                ['employee_id' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'firstname' => $employeeUpdatePersonal->firstname ?? null,
                    'middlename' => $employeeUpdatePersonal->middlename ?? null,
                    'lastname' => $employeeUpdatePersonal->lastname ?? null,
                    'birthdate' => $employeeUpdatePersonal->birthdate ?? null,
                ]
            );
        } 
    }

    public function updateEmploymentData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateEmploymentHistory::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeEmploymentHistory::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'position' => $employeeUpdatePersonal->position ?? null,
                    'department' => $employeeUpdatePersonal->department ?? null,
                    'company_name' => $employeeUpdatePersonal->company_name ?? null,
                    'monthly_salary' => $employeeUpdatePersonal->monthly_salary ?? null,
                    'employment_status' => $employeeUpdatePersonal->employment_status ?? null,
                    'isGovernment' => $employeeUpdatePersonal->isGovernment ?? null,
                    'from_year' => $employeeUpdatePersonal->from_year ?? null,
                    'to_year' => $employeeUpdatePersonal->to_year ?? null,
                ]
            );
        } 
    }

    public function updateCivilServiceData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateCivilService::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeCivilService::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'certification' => $employeeUpdatePersonal->certification ?? null,
                    'rating' => $employeeUpdatePersonal->rating ?? null,
                    'date_exam' => $employeeUpdatePersonal->date_exam ?? null,
                    'place_exam' => $employeeUpdatePersonal->place_exam ?? null,
                    'license_no' => $employeeUpdatePersonal->license_no ?? null,
                    'date_validity' => $employeeUpdatePersonal->date_validity ?? null,
                ]
            );
        } 
    }

    public function updateTrainingData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateTrainings::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeTrainings::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'type' => $employeeUpdatePersonal->type ?? null,
                    'name' => $employeeUpdatePersonal->name ?? null,
                    'date_from' => $employeeUpdatePersonal->date_from ?? null,
                    'date_to' => $employeeUpdatePersonal->date_to ?? null,
                    'consumed_hours' => $employeeUpdatePersonal->consumed_hours ?? null,
                    'sponsored_by' => $employeeUpdatePersonal->sponsored_by ?? null,
                ]
            );
        } 
    }

    public function updateOthersData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateOtherWorks::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeePersonal::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'organization' => $employeeUpdatePersonal->organization ?? null,
                    'address' => $employeeUpdatePersonal->address ?? null,
                    'date_from' => $employeeUpdatePersonal->date_from ?? null,
                    'date_to' => $employeeUpdatePersonal->date_to ?? null,
                    'consumed_hours' => $employeeUpdatePersonal->consumed_hours ?? null,
                    'position' => $employeeUpdatePersonal->position ?? null,
                ]
            );
        } 
    }

    public function updateSkillsData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateSkillsHobbies::where('employee_no', $employee_no)->first();
        if ($employeeUpdatePersonal) {
            // Use updateOrCreate to either update or insert the employee data
            EmployeeSkillsHobbies::updateOrCreate(
                ['employee_no' => $employee_no], // Unique identifier
                [ // Data to update or insert
                    'name' => $employeeUpdatePersonal->name ?? null,
                    'recognition' => $employeeUpdatePersonal->recognition ?? null,
                    'organization' => $employeeUpdatePersonal->organization ?? null,
                ]
            );
        } 
    }

    public function render() {
        return view('livewire.admin.ess.profile-approval.edit');
    }
}
