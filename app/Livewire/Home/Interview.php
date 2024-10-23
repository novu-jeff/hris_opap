<?php

namespace App\Livewire\Home;

use App\Models\InterviewItemsResponses;
use App\Models\JobApplicants;
use App\Models\JobApplicantsInterview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Interview extends Component
{

    public $user_id;
    public $job_id;
    public $applicant_id;
    public $interview_id;
    public $record = [];
    public array $answer;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $id = Auth::guard('applicants')->user()->id;
        $this->user_id = $id;
        
        $applicant = JobApplicants::where('user_id', $id)
            ->where('job_id', $this->job_id)
            ->where('status', 'interview')
            ->first();

        if(!$applicant) {
            return redirect()->route('home.profile.index');
        }

        $this->applicant_id = $applicant->id;

        $record = JobApplicants::with('interview.details', 'interview.items.options', 'interview.items.answers')
            ->first();
 
        if(!$record) {
            return redirect()->route('home.profile.index');
        }

        $this->record = $record;

    }


    protected function rules() {
        return [
            'answer' => 'required',
            'answer.*' => 'required',
        ];
    }

    protected function messages() {
        return [
            'answer.*.required' => 'You answer is required'
        ];
    }

    public function save(bool $isNotify = true) {

        $this->validate();


        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to submit this reponse?',
                'message' => 'Please be informed that once submitted, you\re not be able to edit your responses.',
                'action' => 'save'
            ]);
        }
        
       DB::beginTransaction();
       
        try {

            foreach($this->answer as $interview_item_id => $answers) {
                if(is_array($answers)) {
                    foreach($answers as $answer_id => $answer) {
                        InterviewItemsResponses::insert([
                            'interview_item_id' => $interview_item_id,
                            'answer' => $answer_id
                        ]);
                    }
                } else {
                    InterviewItemsResponses::create([
                        'interview_item_id' => $interview_item_id,
                        'answer' => $answers
                    ]);
                }
            }

            JobApplicants::where('id', $this->applicant_id)
                ->update([
                    'isInterviewResponded' => true
                ]);

            DB::commit();

            $this->loadRecords();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Response Saved!', 
                'showAlert' => true,
                'message' => 'Your response has been recorded and will be viewed by the HR team.'
            ]);

        } catch (\Exception $e) {
            
            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'showAlert' => true,
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }

    }

    public function render()
    {
        return view('livewire.home.interview');
    }
}
