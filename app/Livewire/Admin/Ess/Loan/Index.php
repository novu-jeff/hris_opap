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

    public $viewLoan = null; // to store the selected loan for modal

    protected $listeners = ['remove', 'disapproved', 'approved'];

    protected $paginationTheme = 'bootstrap';
    public $status = 'pending';

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

    

   public function render()
    {
        return view('livewire.admin.ess.loan.index', [
            'loans' => Loan::with(['loanType', 'personal']) // eager load personal info
                ->where('status', $this->status)
                ->paginate(10)
        ]);
    }
}
