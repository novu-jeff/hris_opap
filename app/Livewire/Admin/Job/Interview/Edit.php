<?php

namespace App\Livewire\Admin\Job\Interview;

use App\Models\Interview;
use App\Models\InterviewItems;
use App\Models\InterviewItemsOptions;
use App\Models\JobApplicantsInterview;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{

    public $id;
    protected $listeners = ['ckeditor'];
    public $name;
    public $description;
    public $question;
    public $type = 'simple';
    public $options = [];
    public $interview = [];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        
        $record = Interview::with(['items.options'])->find($this->id);
        
        if(!$record) {
            return redirect()->route('job.interview.index');
        }

        $this->name = $record->name;
        $this->description = $record->description;

        foreach($record->items as $key => $item) {
            $this->interview[$key] = [
                'question' => $item['question'],
                'type' => $item['response_type']
            ];

            if(in_array($item['response_type'], ['checkbox', 'radio'])) {
                foreach($item['options'] as $option) {
                    $this->interview[$key]['options'][] = $option->name;
                }
            }
        }


    }

    public function ckeditor($data) {
        $this->description = $data;
    }

    public function add_item() {
        $this->dispatch('showModal', [
            'modal' => 'add-item'
        ]);
    }

    public function remove_item(int $index) {
        unset($this->interview[$index]);
    }

    public function setItemType(string $type) {
        $this->type = $type;
    }

    public function add_option() {
        $this->options[] = '';
    }

    public function remove_option(int $index) {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function save_interview() {

        $this->validate([
            'question' => 'required|string|max:255',
            'type' => 'required|string|in:simple,explanatory,checkbox,radio,file',
            'options' => 'required_if:type,checkbox,radio|array',
            'options.*' => 'required|string|max:255'
        ],[
            'question.required' => 'The interview question is required.',
            'question.string' => 'The interview question must be a valid string.',
            'question.max' => 'The interview question may not be greater than 255 characters.',
            
            'type.required' => 'Please select a response type.',
            'type.in' => 'The selected response type is invalid.',

            'options.required_if' => 'At least one option is required when the type is checkbox or radio.',
            'options.*.required' => 'Each option is required.',
            'options.array' => 'Options must be an array.',
            'options.*.string' => 'Each option must be a valid string.',
            'options.*.max' => 'Each option may not be greater than 255 characters.',
        ]);


        $data = [
            'question' => $this->question,
            'type' => $this->type,
            'options' => $this->options,
        ];


        $this->interview[] = $data;

        $this->reset('question', 'type', 'options');

        $this->dispatch('hideModal', [
            'modal' => 'add-item'
        ]);

        

    }

    public function save() {

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'interview' => 'required|array'
        ], [
            'interview.required' => 'Please make atleast one interview question.'
        ]);
       
        DB::beginTransaction();

        try {

            Interview::where('id', $this->id)->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            
            InterviewItems::where('interview_id', $this->id)->delete();

            $record = JobApplicantsInterview::with('applicant')->first();
            
            if ($record && $record->applicant) {
                $record->applicant->isInterviewResponded = false;
                $record->applicant->save();
            }

            foreach ($this->interview as $item) {
                $interview_item = InterviewItems::create([
                    'interview_id' => $this->id,
                    'question' => $item['question'],
                    'response_type' => $item['type']
                ]);
            
                if ($item['type'] === 'checkbox' || $item['type'] === 'radio') {
                    foreach ($item['options'] as $options) {
                        InterviewItemsOptions::create([ 
                            'interview_item_id' => $interview_item->id,
                            'name' => $options
                        ]);
                    }
                }
            }

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Interview was added successfully'
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
        return view('livewire.admin.job.interview.edit');
    }
}
