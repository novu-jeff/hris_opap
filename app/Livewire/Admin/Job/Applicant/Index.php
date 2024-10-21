<?php

namespace App\Livewire\Admin\Job\Applicant;

use App\Http\Controllers\Admin\Services\HRISProcessingService;
use App\Models\Interview;
use App\Models\JobApplicants;
use App\Models\JobApplicantsInterview;
use App\Models\JobApplicantsOffer;
use App\Models\JobApplicantsRequirements;
use App\Models\JobRequirements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{

    use WithFileUploads;

    public $records;
    public $status;
    public $applicant_information;
    public $selected_id;
    public $interview;
    public $selected_interview = [];
    public $applicant_responses;
    public $job_offer = [];
    public $requirements;
    public $selected_requirements = [];

    protected $listeners = [
        'ckeditor', 
        'set_placement', 
        'send_offer', 
        'set_onboarding',
        'set_hired',
        'reject',
        'delete'
    ];

    public function mount() {
        $this->loadRecords();
    }

    public function ckeditor($data) {
        $this->job_offer['body'] = $data;
    }

    public function loadRecords() {
        $this->records = JobApplicants::with(['applicant', 'job', 'offer', 'requirements'])
            ->where('status', $this->status)
            ->get();
    }

    # view applicants

    public function view_applicant(int $id) {

        $record = JobApplicants::with(['applicant', 'job'])
            ->where('id', $id);

        if(!$record->exists()) {
            
        }

        $this->applicant_information = $record->first()->applicant;
        $this->dispatch('showModal', [
            'modal' => 'applicant_info'
        ]);

    }

    # view responses from the interview
    public function view_responses(int $id) {
        $records = JobApplicantsInterview::with(['interview.items'])
            ->where('job_applicants_id', $id)->get();
        $this->applicant_responses = $records;
        $this->dispatch('showModal', [
            'modal' => 'applicant_responses'
        ]);
    }

    public function set_action(string $action, int $id) {
        if($this->validate_action($action)) {
            $this->selected_id = $id;
            switch($action) {
                case 'process':
                    $this->process($id);
                    break;
                case 'rejected':
                    $this->reject($id);
                    break;
                case 'delete':
                    $this->delete($id);
                    break;
            }
        }
    }

    public function process(int $id) {
        $model = JobApplicants::find($id);
        $current_status = $model->status;
        
        if($current_status == 'pending') {
            $this->set_interview();
        } elseif($current_status == 'interview') {
            $this->set_placement(true);
        } elseif($current_status == 'placement') {
            $this->set_onboarding(true);
        } elseif($current_status == 'onboarding') {
            $this->set_hired(true);
        } else {
            dd('why are you here?');
        }
    }

    # set requirements checklist
    public function set_checklist($isSaved, int $id = null) {
        if(!$isSaved) {
            $records = JobRequirements::all();
            
            $savedRequirements = JobApplicantsRequirements::where('job_applicants_id', $id)
                ->get();

            $this->requirements = $records;
            foreach($savedRequirements as $key => $value) {
                $this->selected_requirements[$value->requirement_id] = true;
            }

            $this->selected_id = $id;

            return $this->dispatch('showModal', [
                'modal' => 'applicant_requirements',
                'plugins' => [
                    'ckeditor'
                ]
            ]);
        }
            
        JobApplicantsRequirements::where('job_applicants_id', $this->selected_id)
            ->delete();


        foreach ($this->selected_requirements as $key => $item) {
            
            if($item === true) {

                $record = JobRequirements::find($key);     

                if ($record) {
                    JobApplicantsRequirements::insert([
                        'job_applicants_id' => $this->selected_id,
                        'requirement_id' => $record->id,
                    ]);
                } else {
                    return $this->dispatch('alert', [
                        'status' => 'error',
                        'title' => 'Oops', 
                        'isRemoveRowDT' => false,
                        'showAlert' => true,
                        'message' => 'The selected requirement does not exist.'
                    ]);
                }
            }
            
        }
        
        $this->loadRecords();
        return $this->dispatch('alert', [
            'id' => $this->selected_id,
            'status' => 'success',
            'title' => 'Success!', 
            'isRemoveRowDT' => false,
            'isReloadDT' => false,
            'message' => 'Requirements were marked successfully.' 
        ]);

    }

    # set an interview / test to applicant
    public function set_interview(bool $isNotify = true) {
        if($isNotify) {
            $this->interview = Interview::get();
            $this->interview = $this->interview->isNotEmpty() ? $this->interview : null;
            $this->dispatch('showModal', [
                'modal' => 'select_interview'
            ]);
        } else {

            $model = JobApplicants::find($this->selected_id);
            
            if(empty($this->selected_interview)) {
                return $this->dispatch('alert', [
                    'status' => 'error',
                    'title' => 'Oops', 
                    'isRemoveRowDT' => false,
                    'showAlert' => true,
                    'message' => 'Please select atleast one interview'
                ]);
            }
            
            foreach($this->selected_interview as $key => $item) {
                $record = Interview::where('id', $key);
                if($record->exists()) {
                    JobApplicantsInterview::insert([
                        'job_applicants_id' => $this->selected_id,
                        'job_interview_id' => $key
                    ]);
                } else {
                    return $this->dispatch('alert', [
                        'status' => 'error',
                        'title' => 'Oops', 
                        'isRemoveRowDT' => false,
                        'showAlert' => true,
                        'message' => 'The selected interview does not exists'
                    ]);
                }
            }

            $model->update([
                'status' => 'interview',
            ]);

            $this->loadRecords();
            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been moved to interview.' 
            ]);

        }
    }

    # set application to placement
    public function set_placement(bool $isNotify = true) {
        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'set_placement';
            $this->notify($title, $message, $action);
        } else {
            $model = JobApplicants::find($this->selected_id);
            $model->update([
                'status' => 'placement',
            ]);
            return $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Yey!', 
                'message' => 'Application has been moved to placement.',
                'isRemoveRowDT' => true,
            ]);
        }
    }

    # set application to onboarding
    public function set_onboarding(bool $isNotify = true) {
        $model = JobApplicants::with('offer')->find($this->selected_id);

        if(is_null($model->offer)) {
            return $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Unable to set to onboarding. No job offer has been sent to the applicant. Please send one first.',
                'isRemoveRowDT' => false,
            ]);
        }

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'set_onboarding';
            $this->notify($title, $message, $action);
        } else {
            $model->update([
                'status' => 'onboarding',
            ]);
    
            $this->loadRecords();
            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been moved to onboarding.' 
            ]);
        }
    
    }

    # set application to hired
    public function set_hired(bool $isNotify = true) {

        $model = JobApplicants::with('requirements')->find($this->selected_id);

        if($model->requirements->count() < 3) {
            return $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Unable to hire this application. No requirements submitted. Applicant must submit atleast three (3) requirements.',
                'isRemoveRowDT' => false,
            ]);
        }

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'set_hired';
            $this->notify($title, $message, $action);
        } else {

            if($this->create_employee()) {
                // $model->update([
                //     'status' => 'hired',
                // ]);
    
                $this->loadRecords();
                $this->dispatch('alert', [
                    'id' => $this->selected_id,
                    'status' => 'success',
                    'title' => 'Success!', 
                    'isRemoveRowDT' => true,
                    'showAlert' => true,
                    'message' => 'Applicant has been hired!.' 
                ]);
            }
        }
    
    }

    # send offer in under placement
    public function send_offer($isSaved, int $id = null) {
        if(!$isSaved) {
            $this->selected_id = $id;
            return $this->dispatch('showModal', [
                'modal' => 'applicant_job_offer',
                'plugins' => [
                    'ckeditor'
                ]
            ]);
        }

        $rules = [
            'subject' => 'required',
            'body' => 'required',
            'attachment' => 'required|file|mimes:docx,doc,pdf',
        ];

        $validator = Validator::make($this->job_offer, $rules);

        if($validator->fails()) {
            return $this->setErrorBag($validator->errors());
        }

        $model = JobApplicants::with('job')->find($this->selected_id);
        
        $attachment = $this->job_offer['attachment'];
        $extension = $attachment->getClientOriginalExtension();
        $filename = 'job_offer_' . str_replace(' ', '_', $model->job->position 
            . '_' . time()) 
            . '.' . $extension;

        $attachment->storeAs('public/applicant/users/' . $model->user_id . '/offers', $filename);

        JobApplicantsOffer::insert([
            'job_applicants_id' => $this->selected_id,
            'subject' => $this->job_offer['subject'],
            'body' => $this->job_offer['body'],
            'attachment' => $filename,
        ]); 

        return $this->dispatch('alert', [
            'id' => $this->selected_id,
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Yey!', 
            'message' => 'Job offer has been sent to the applicant.',
            'isRemoveRowDT' => false,
            'isReloadDT' => true,
        ]);

    }

    public function notify(string $title, string $message, string $action) {
        $this->dispatch('showConfirmation', [
            'title' => $title,
            'message' => $message,
            'action' => $action
        ]);
    }

    public function reject(bool $isNotify = true) {

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'reject';
            $this->notify($title, $message, $action);
        } else {

            $record = JobApplicants::find($this->selected_id);

            if(!$record) {
                return $this->dispatch('alert', [
                    'id' => $this->selected_id,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Job application does not exists' 
                ]);
            }

            $record->update([
                'status' => 'rejected'
            ]);
         
            $this->loadRecords();
            $this->dispatch('alert', [
                'id' => $record->id,
                'status' => 'success',
                'title' => 'Success', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been rejected'
            ]);

        }

    }

    public function delete(bool $isNotify = true) {

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'delete';
            $this->notify($title, $message, $action);
        } else {

            $record = JobApplicants::where('id', $this->selected_id);

            if(!$record) {
                return $this->dispatch('alert', [
                    'id' => $this->selected_id,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Job application does not exists' 
                ]);
            }

            $record->delete();
    
            $this->loadRecords();
            $this->dispatch('alert', [
                'id' => $record->id,
                'status' => 'success',
                'title' => 'Success!', 
                'isRemoveRowDT' => true,
                'message' => 'Applicantion has been deleted!.' 
            ]);

        }
    
    }

    public function validate_action(string $action) {
        $allowed = ['process', 'rejected', 'delete'];
        if(in_array($action, $allowed)) {
            return true;
        }
        return false;
    }

    public function create_employee() {

        $process = new HRISProcessingService();
        
        DB::beginTransaction();
        
        $record = JobApplicants::find($this->selected_id);

        if(!$record) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . 'Job applicant id does not exists'
            ]);
        }

        try {
            $process->save(true, $record->user_id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'isRemoveRowDT' => false,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage() 
            ]);
        }

    }

    public function render()
    {
        return view('livewire.admin.job.applicant.index');
    }
}
