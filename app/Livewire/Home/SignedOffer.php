<?php

namespace App\Livewire\Home;

use App\Models\InterviewItemsResponses;
use App\Models\JobApplicants;
use App\Models\JobApplicantsInterview;
use App\Models\JobApplicantsOffer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class SignedOffer extends Component
{

    use WithFileUploads;

    public $user_id;
    public $job_id;
    public $record = [];
    public $offer;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $id = Auth::guard('applicants')->user()->id;
        $this->user_id = $id;
        
        $record = JobApplicants::with('job', 'offer')
            ->where('user_id', $id)
            ->where('job_id', $this->job_id)
            ->where('status', 'placement')
            ->first();

        if(!$record) {
            return redirect()->route('home.profile.index');
        }

        $this->record = $record;

    }


    protected function rules() {
        return [
            'offer' => 'required|file|mimes:doc,docx,docs,pdf'
        ];
    }
    
    protected $messages = [
        'offer.required' => 'The file upload is required.',
        'offer.file' => 'The uploaded item must be a file.',
        'offer.mimes' => 'The file must be a document of type: doc, docx, docs, pdf.',
    ];
    
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

            $attachment = $this->offer;
            $extension = $attachment->getClientOriginalExtension(); 
            $filename = 'signed_job_offer_' . str_replace(' ', '_', $this->record->job->position 
                . '_' . time()) 
                . '.' . $extension;
            $attachment->storeAs('public/applicant/users/' . $this->user_id .'/' . $this->job_id . '/offers', strtolower($filename));


            JobApplicantsOffer::where('id', $this->record->offer->id)
                ->update([
                    'signed_attachment' => strtolower($filename)
                ]);

            JobApplicants::where('id', $this->user_id)
                ->where('job_id', $this->job_id)
                ->update([
                    'isSignedJobOffer' => true
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
        return view('livewire.home.signed-offer');
    }
}
