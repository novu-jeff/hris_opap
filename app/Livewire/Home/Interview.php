<?php

namespace App\Livewire\Home;

use App\Models\JobApplicants;
use App\Models\JobApplicantsInterview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Interview extends Component
{

    public $user_id;
    public $job_id;
    public $interview_id;
    public $record = [];
    public array $answer = [];

    public function mount() {

        $id = Auth::guard('applicants')->user()->id;
        $this->user_id = $id;
        
        $applicant = JobApplicants::where('user_id', $id)
            ->where('job_id', $this->job_id)
            ->where('status', 'interview')
            ->first();

        if(!$applicant) {
            return redirect()->route('home.profile');
        }

        $record = JobApplicantsInterview::with('interview.items')
            ->where('job_applicants_id', $applicant->id)
            ->where('job_interview_id', $this->interview_id)->first();

        if(!$record) {
            return redirect()->route('home.profile.index');
        }

        $this->record = $record;

        foreach ($this->record->interview->items as $key => $item) {
            $this->answer[$key] = ''; 
        }

    }


    protected function rules() {
        return [
            'answer.*' => 'required',
        ];
    }

    protected function messages() {
        return [
            'answer.*.required' => 'You answer is required'
        ];
    }

    public function save() {
        $this->validate();
        dd($this->answer);
    }

    public function render()
    {
        return view('livewire.home.interview');
    }
}
