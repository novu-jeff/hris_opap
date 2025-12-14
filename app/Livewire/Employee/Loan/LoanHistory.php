<?php

namespace App\Livewire\Employee\Loan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class LoanHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $entries = 10;
    public $status = 'all';
    public $employee_no;

    protected $queryString = ['entries', 'status'];

    protected $listeners = ['CancelLoan'];

    public function mount()
    {
        $this->employee_no = Auth::user()->employee_no;
    }

    public function updatedEntries()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function confirmCancelLoan(int $loanId)
    {
       
        $this->dispatch('showConfirmation', [
            'title' => 'Cancel Loan',
            'message' => 'Are you sure you want to cancel this loan? This action cannot be undone.',
            'action' => 'cancelLoan',   // The method to call if confirmed
            'params' => [$loanId] // ✅ REQUIRED
        ]);
    }

    public function CancelLoan(bool $isNotify = true, ? int $loanId = null)
    {
         if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Are you sure you want to cancel this loan? This action cannot be undone.';
            $action = 'cancel';

           // $this->selected_id = $loanId;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action,
                 'params' => [$loanId] // ✅ REQUIRED
            ]);

        }  else {

            // Proceed with cancellation after confirmation
            $loan = Loan::where('id', $loanId)
                        ->where('employee_no', $this->employee_no)
                        ->firstOrFail();

            if ($loan->status !== 'pending') {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Cannot Cancel',
                    'message' => 'Only pending loans can be cancelled.'
                ]);
            }

            $loan->update(['status' => 'cancelled']);

            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Cancelled',
                'message' => 'The loan has been cancelled successfully.'
            ]);

            // Refresh pagination to reflect changes
            $this->resetPage();
        }    

    }


    public function getRecordsProperty()
    {
        return Loan::with('loanType')
            ->where('employee_no', $this->employee_no)
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderByDesc('created_at')
            ->paginate($this->entries);
    }

    public function render()
    {
        return view('livewire.employee.loan.loan-history', [
            'records' => $this->records
        ]);
    }
}
