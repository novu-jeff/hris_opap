<?php

namespace App\Livewire\Admin\Settings\Tranches;

use App\Models\Tranche;
use Illuminate\Support\Facades\Gate;
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
    public $show;
    public $showWtax = true; 


    public function remove(bool $isNotify = true, int $id = null) {

        if (Gate::denies('write tranches')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this tranche. Once this action is completed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = Tranche::find($this->selected_id);
                
            if($record) {
                
                $record->isDeleted = true;
                $record->year = '0000';
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Tranche ' . strtoupper($record->name) . ' was deleted successfully' 
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

    public function view(int $id) {

        $records = Tranche::with(['items', 'employmentType'])
            ->where('id', $id)
            ->first(); 

        if($records) {

            $this->show = $records;

            $this->dispatch('showModal', [
                'modal' => 'show'
            ]);
        }
    }

    public function render()
    {

       $model = Tranche::with(['items', 'employmentType'])
        ->where('isDeleted', false);

        if ($this->search) {
            $this->resetPage();
            $model = $model->where('name', 'like', '%' . $this->search . '%');
        }

        // Get all tranches
        $records = $model->latest()->get();

        // Group tranches by year
        $tranchesByYear = $records->groupBy('year');

        return view('livewire.admin.settings.tranches.index', [
            'tranchesByYear' => $tranchesByYear,
        ]);
    }
}
