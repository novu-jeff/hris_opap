<?php

namespace App\Livewire\Admin\Ess\Faqs;

use App\Models\FAQs;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;
    
    public $selected_id;
    protected $listeners = ['remove'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this FAQ. Once this action is processed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = FAQs::find($this->selected_id);
                
            if($record) {
                
                $record->isDeleted = true;
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'FAQ was deleted successfully' 
                ]);
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }
    }

    public function render()
    {

        $model = FAQs::where('isDeleted', false);

        if ($this->search) {
            $this->resetPage(); 
            $model->where('name', 'like', '%' . $this->search . '%');
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.ess.faqs.index', [
            'records' => $records
        ]);
    }
}