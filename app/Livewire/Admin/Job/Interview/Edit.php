<?php

namespace App\Livewire\Admin\Job\Interview;

use App\Models\Interview;
use App\Models\InterviewItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Edit extends Component
{

    protected $listeners = ['ckeditor'];
    public $id;
    public $name;
    public $level;
    public $description;
    public $items = [''];
    public $item_count = 1;

    public function mount() {
        $this->pullRecords();
    }

    public function pullRecords() {
        $record = Interview::with(['items'])->find($this->id);
        if(!$record) {
            return redirect()->route('job.interview.index');
        }

        $this->name = $record->name;
        $this->level = $record->level;
        $this->description = $record->description;

        $this->item_count = $record->items->count();

        foreach($record->items as $key => $item) {
            $this->items[$key] = $item->name;
        }
        
    }

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

            Interview::where('id', $this->id)
                ->update([
                'name' => $this->name,
                'level' => $this->level,
                'description' => $this->description,
            ]);

           
            InterviewItems::where('interview_id', $this->id)->delete();

            foreach($this->items as $item) {
                InterviewItems::insert(values: [
                    'interview_id' => $this->id,
                    'name' => $item,
                ]);
            }

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'message' => 'Interview was added successfully'
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
        return view('livewire.admin.job.interview.edit');
    }
}
