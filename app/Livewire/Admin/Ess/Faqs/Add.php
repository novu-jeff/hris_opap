<?php

namespace App\Livewire\Admin\Ess\Faqs;

use App\Models\FAQs;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Add extends Component
{

    use WithFileUploads;

    public $name;
    public $description;
    public $record_id;
    public $user_id;

    protected $listeners = ['ckeditor', 'save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        if(!is_null($this->record_id)) {
            $record = FAQs::where('id', $this->record_id)
                ->first();
        
            if(!$record) {
                return redirect()
                    ->route('ess.faqs.index');
            }

            $this->name = $record->name;
            $this->description = $record->description;            
        }
   
    }


    public function rules() {
        return [
            'name' => 'required',
            'description' => 'required',
        ];
    }

    public function ckeditor($data) {
        $this->description = $data;
    }
    
    public function save(bool $isNotify = true) {
        
        $this->validate();

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {

            try {    
                
                $existingRecord = FAQs::where('id', $this->record_id)->first();

                $model = FAQs::updateOrCreate([
                    'id' => $this->record_id,
                ], [
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
                
                $action = $existingRecord ? 'updated' : 'added';
                

                if(is_null($this->record_id)) {

                    $this->resetExcept('user_id');

                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'FAQ was added successfully.',
                        'redirect' => route('ess.faqs.create')
                    ]);

                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'FAQ has been updated.'
                    ]);
                }

            } catch (\Exception $e) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops', 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }

        }


    }

    public function render()
    {
        return view('livewire.admin.ess.faqs.add');
    }
}
