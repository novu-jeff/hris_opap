<?php

namespace App\Livewire\Admin\Job\Interview;

use App\Models\Interview;
use App\Models\InterviewItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Create extends Component
{

    protected $listeners = ['ckeditor'];
    public $name;
    public $level;
    public $description;
    public $items = [''];
    public $item_count = 1;

    public function ckeditor($data) {
        $this->description = $data;
    }

    public function add_item() {
        $this->item_count += 1;
        $this->items[] = '';
    }

    public function remove_item($index) {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->item_count -= 1;
    }

    protected function rules() {
        return [
            'name' => 'required',
            'level' => 'integer|lt:6',
            'description' => 'required',
            'items.*' => 'required|string',
        ];
    }

    protected function messages() {
        return [
            'items.*.required' => 'This field is required'
        ];
    }

    public function save() {

        $this->validate();
       

        DB::beginTransaction();

        try {

            $interview = Interview::create([
                'name' => $this->name,
                'level' => $this->level,
                'description' => $this->description,
            ]);

            
            foreach($this->items as $item) {
                InterviewItems::insert([
                    'interview_id' => $interview->id,
                    'name' => $item,
                ]);
            }

            DB::commit();

            $this->reset();

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
        return view('livewire.admin.job.interview.create');
    }
}
