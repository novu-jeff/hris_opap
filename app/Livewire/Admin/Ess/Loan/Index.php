<?php

namespace App\Livewire\Admin\Ess\Loan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Loan;

class Index extends Component
{
    use WithPagination;

    public $selectedLoanId;
    public $disapproval_note = '';
    public $selected_id;
    public $employee_no;
    public $search = '';  // Add this for search
    public $entries = 10; // optional, for show entries dropdown

    public $viewLoan = null; // to store the selected loan for modal

    protected $listeners = ['remove', 'disapproved', 'approved'];

    protected $paginationTheme = 'bootstrap';
    public $status = 'pending';

    public function mount()
    {
        // validate status from URL
       // dd($this->status);
        if (!in_array($this->status, ['pending', 'approved', 'disapproved'])) {
            $this->status = 'pending';
        }
    }

    public function approve(int $loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->status !== 'pending') return;

        $loan->update(['status' => 'approved']);

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Approved',
            'message' => 'Loan approved successfully.'
        ]);
    }

      public function disapproved(bool $isNotify = true)
    {
        if ($isNotify) {
           

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to disapprove this loan application <b>#' . strtoupper($this->employee_no) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'disapproved';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action, 
            ]);
        } else {
         

            $loan = Loan::where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();

            if (!$loan) return;

            $loan->status = 'disapproved';
            $loan->disapproval_note = $this->disapproval_note ?? null;
            $loan->save();

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Disapproved',
                'message' => 'Loan application has been disapproved.'
            ]);
        }
    }


    public function view(int $loanId)
    {
        $this->selected_id = $loanId;
        $this->viewLoan = Loan::with(['loanType', 'personal'])->findOrFail($loanId);
        $this->employee_no = $this->viewLoan->employee_no;

        // trigger modal
        return $this->dispatch('showModal', [
                'modal' => 'showModal', 
            ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEntries()
    {
        $this->resetPage();
    }

    

   public function render()
{
    $model = Loan::with(['loanType', 'personal'])
        ->where('status', $this->status);

    if ($this->search) {
        $this->resetPage();

        $model->where(function ($query) {
            $query->where('employee_no', 'like', '%' . $this->search . '%');
                
        });
    }

    $loans = $model->latest()->paginate($this->entries);

    return view('livewire.admin.ess.loan.index', compact('loans'));
}

}
