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
    public $answer;
    public $activeTab;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $id = Auth::guard('applicant')->user()->id;
        $this->user_id = $id;

        
        $record = JobApplicants::with('job', 'interview.details', 'interview.items.options', 'interview.items.answers')
            ->where('user_id', $id)
            ->where('job_id', $this->job_id)
            ->where('status', 'interview')
            ->first();


        if(!$record) {
            return redirect()->route('home.profile');
        }

        $this->applicant_id = $record->id;
        $this->record = $record;

        foreach ($record->interview as $interview) {
            foreach ($interview->items as $item) {
                if (in_array($item->response_type, ['checkbox', 'radio'])) {
                    if (!isset($this->answer[$item->id])) {
                        $this->answer[$item->id] = [];
                    }
                    foreach ($item->answers as $answer) {
                        $this->answer[$item->id][$answer['answer']] = '';
                    }
                } else {
                    $this->answer[$item->id] = '';
                }
            }
        }
    }

    protected function rules() {
        return [
            'answer' => 'required|array',
            'answer.*' => 'required',
            'answer.*.*' => 'required', 
        ];
    }
    
    protected function messages() {
        return [
            'answer.required' => 'Answer is required.',
            'answer.*' => 'Answer is required.',
            'answer.*.*.required' => 'Answer is required.',
        ];
    }

    public function go_back() {
        session()->put('target', [
            'page' => 'profile',
            'tab' => 'interview',
            'accordion' => '',
        ]);
        return redirect()->route('home.profile');
    }

    public function setActive(int $tab) {
        $this->activeTab = $tab;
    }
    

    public function save(bool $isNotify = true) {

        $this->validate();

        if($isNotify) {
            return $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to submit this response?',
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
                            'user_id' => $this->user_id,
                            'interview_item_id' => $interview_item_id,
                            'answer' => $answer_id
                        ]);
                    }
                } else {
                    InterviewItemsResponses::create([
                        'user_id' => $this->user_id,
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

            $this->answer = [];

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
