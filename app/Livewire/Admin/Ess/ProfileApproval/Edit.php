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
use Illuminate\Support\Facades\DB;
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
    protected $listeners = ['approve', 'reject'];

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
            return redirect()->route('ess.approval-profile.index');
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
            $action = 'approve';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {

            DB::beginTransaction();

            try {

                $this->updatePersonalData($this->employee_no);
                $this->updateEducationData($this->employee_no);
                $this->updateParentsData($this->employee_no);
                $this->updateChildrenData($this->employee_no);
                $this->updateEmploymentData($this->employee_no);
                $this->updateCivilServiceData($this->employee_no);
                $this->updateTrainingData($this->employee_no);
                $this->updateOthersData($this->employee_no);
                $this->updateSkillsData($this->employee_no);

                $this->remove();
                

                DB::commit();

                return $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'isRemoveRowDT' => false,
                    'isReloadDT' => false,
                    'message' => 'Employee ' . strtoupper($this->employee_no) . ' was approved successfully.',
                    'redirect' => route('ess.approval-profile.index')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function reject(bool $isNotify = true) {
        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'reject';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            DB::beginTransaction();

            try {
                $this->remove();

                DB::commit();

                return $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'isRemoveRowDT' => false,
                    'isReloadDT' => false,
                    'message' => 'Employee ' . strtoupper($this->employee_no) . ' was rejected for updating profile.',
                    'redirect' => route('ess.approval-profile.index')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }

        }
    }

    public function updatePersonalData($employee_no) {
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
        $employeeUpdatePersonal = EmployeeUpdateEducation::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeEducation::updateOrCreate(
                ['employee_no' => $employee_no, 'school_name' => $personal->school_name], // Unique identifier, adding school_name to differentiate entries
                [
                    'level' => $personal->level ?? null,
                    'school_name' => $personal->school_name ?? null,
                    'course' => $personal->course ?? null,
                    'from_year' => $personal->from_year ?? null,
                    'to_year' => $personal->to_year ?? null,
                ]
            );
        }
    }
    
    public function updateParentsData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateParents::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeParents::updateOrCreate(
                ['employee_no' => $employee_no, 'spouse_surname' => $personal->spouse_surname], // Unique identifier, adding spouse_surname to differentiate entries
                [
                    'spouse_surname' => $personal->spouse_surname ?? null,
                    'spouse_firstname' => $personal->spouse_firstname ?? null,
                    'spouse_middlename' => $personal->spouse_middlename ?? null,
                    'spouse_suffix' => $personal->spouse_suffix ?? null,
                    'father_surname' => $personal->father_surname ?? null,
                    'father_firstname' => $personal->father_firstname ?? null,
                    'father_middlename' => $personal->father_middlename ?? null,
                    'father_suffix' => $personal->father_suffix ?? null,
                    'father_business_name' => $personal->father_business_name ?? null,
                    'father_business_address' => $personal->father_business_address ?? null,
                    'father_tel_no' => $personal->father_tel_no ?? null,
                    'mother_surname' => $personal->mother_surname ?? null,
                    'mother_firstname' => $personal->mother_firstname ?? null,
                    'mother_middlename' => $personal->mother_middlename ?? null,
                    'mother_occupation' => $personal->mother_occupation ?? null,
                    'mother_business_name' => $personal->mother_business_name ?? null,
                    'mother_business_address' => $personal->mother_business_address ?? null,
                    'mother_tel_no' => $personal->mother_tel_no ?? null,
                ]
            );
        }
    }
    
    public function updateChildrenData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateChildren::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeChildren::updateOrCreate(
                ['employee_no' => $employee_no, 'firstname' => $personal->firstname], // Unique identifier, adding firstname to differentiate entries
                [
                    'firstname' => $personal->firstname ?? null,
                    'middlename' => $personal->middlename ?? null,
                    'lastname' => $personal->lastname ?? null,
                    'birthdate' => $personal->birthdate ?? null,
                ]
            );
        }
    }
    
    public function updateEmploymentData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateEmploymentHistory::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeEmploymentHistory::updateOrCreate(
                ['employee_no' => $employee_no, 'company_name' => $personal->company_name], // Unique identifier, adding company_name to differentiate entries
                [
                    'position' => $personal->position ?? null,
                    'department' => $personal->department ?? null,
                    'company_name' => $personal->company_name ?? null,
                    'monthly_salary' => $personal->monthly_salary ?? null,
                    'employment_status' => $personal->employment_status ?? null,
                    'isGovernment' => $personal->isGovernment ?? null,
                    'from_year' => $personal->from_year ?? null,
                    'to_year' => $personal->to_year ?? null,
                ]
            );
        }
    }
    
    public function updateCivilServiceData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateCivilService::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeCivilService::updateOrCreate(
                ['employee_no' => $employee_no, 'license_no' => $personal->license_no], // Unique identifier, adding license_no to differentiate entries
                [
                    'certification' => $personal->certification ?? null,
                    'rating' => $personal->rating ?? null,
                    'date_exam' => $personal->date_exam ?? null,
                    'place_exam' => $personal->place_exam ?? null,
                    'license_no' => $personal->license_no ?? null,
                    'date_validity' => $personal->date_validity ?? null,
                ]
            );
        }
    }
    
    public function updateTrainingData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateTrainings::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeTrainings::updateOrCreate(
                ['employee_no' => $employee_no, 'name' => $personal->name], // Unique identifier, adding name to differentiate entries
                [
                    'type' => $personal->type ?? null,
                    'name' => $personal->name ?? null,
                    'date_from' => $personal->date_from ?? null,
                    'date_to' => $personal->date_to ?? null,
                    'consumed_hours' => $personal->consumed_hours ?? null,
                    'sponsored_by' => $personal->sponsored_by ?? null,
                ]
            );
        }
    }
    
    public function updateOthersData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateOtherWorks::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeePersonal::updateOrCreate(
                ['employee_no' => $employee_no, 'organization' => $personal->organization], // Unique identifier, adding organization to differentiate entries
                [
                    'organization' => $personal->organization ?? null,
                    'address' => $personal->address ?? null,
                    'date_from' => $personal->date_from ?? null,
                    'date_to' => $personal->date_to ?? null,
                    'consumed_hours' => $personal->consumed_hours ?? null,
                    'position' => $personal->position ?? null,
                ]
            );
        }
    }
    
    public function updateSkillsData($employee_no) {
        $employeeUpdatePersonal = EmployeeUpdateSkillsHobbies::where('employee_no', $employee_no)->get(); // Use get() for multiple rows
    
        foreach ($employeeUpdatePersonal as $personal) {
            EmployeeSkillsHobbies::updateOrCreate(
                ['employee_no' => $employee_no, 'name' => $personal->name], // Unique identifier, adding name to differentiate entries
                [
                    'name' => $personal->name ?? null,
                    'recognition' => $personal->recognition ?? null,
                    'organization' => $personal->organization ?? null,
                ]
            );
        }
    }
    

    public function remove() {

        $record = EmployeeUpdatePersonal::where('employee_no', $this->employee_no)->first();

        if($record) {

            $record->education()->delete();
            $record->parents()->delete();
            $record->children()->delete();
            $record->employment_history()->delete();
            $record->civil_service()->delete();
            $record->trainings()->delete();
            $record->others()->delete();
            $record->skills()->delete();
            
            $record->delete();

            return true;

        } else {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'isRemoveRowDT' => false,
                'message' => 'Error: ID does not exists' 
            ]);
        }

    }

    public function render() {
        return view('livewire.admin.ess.profile-approval.edit');
    }
}
