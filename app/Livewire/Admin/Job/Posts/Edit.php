<?php

namespace App\Livewire\Admin\Job\Posts;

use App\Models\JobPosts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Edit extends Component
{

    protected $listeners = ['ckeditor'];

    public $id;
    public $position;
    public $company_name;
    public $location;
    public $setup;
    public $type;
    public $slot;
    public $min_salary;
    public $max_salary;
    public $description;

    public function mount() {
        $this->pullRecords();
    }

    public function pullRecords() {
        $record = JobPosts::find($this->id);
        if(!$record) {
            return redirect()->route('job.posts.index');
        }

        $this->position = $record->position;
        $this->company_name = $record->company_name;
        $this->location = $record->location;
        $this->setup = $record->setup;
        $this->type = $record->type;
        $this->slot = $record->slots;
        $this->min_salary = $record->min_salary;
        $this->max_salary = $record->max_salary;
        $this->description = $record->description;

    }

    public function ckeditor($data) {
        $this->description = $data;
    }

    // form submit  
    public function save() {
    
        $data = [
            'id' => $this->id,
            'position' => $this->position,
            'company_name' => $this->company_name,
            'location' => $this->location,
            'setup' => $this->setup,
            'type' => $this->type,
            'slot' => $this->slot,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'description' => $this->description,
        ];

        $rules =  [
            'id' => 'required|exists:job_posts',
            'position' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'setup' => 'required|string',
            'type' => 'required|string',
            'slot' => 'required|integer|min:1|max:10',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0|gt:min_salary',
            'description' => 'required|string',
        ];


        $validator = Validator::make($data, $rules);
        
        if($validator->fails()) {
            $this->dispatch('alert', [
                'status' => 'error',
                'showAlert' => false,
                'resetFields' => false,
            ]);

            $this->setErrorBag([]);
            
            foreach ($validator->errors()->toArray() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return;
        };


        
        DB::beginTransaction();

        try {

            JobPosts::where('id', $this->id)
                ->update([
                    'position' => $this->position,
                    'company_name' => $this->company_name,
                    'location' => $this->location,
                    'setup' => $this->setup,
                    'type' => $this->type,
                    'min_salary' => $this->min_salary,
                    'max_salary' => $this->max_salary,
                    'description' => $this->description,
                    'slots' => $this->slot,
                ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'resetFields' => false,
                'message' => 'Job as ' . strtoupper($this->position) . ' at ' . strtoupper($this->company_name) . ' has been updated!',
            ]);
            
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }

    }

    public function render()
    {
        return view('livewire.admin.job.posts.edit');
    }
}
