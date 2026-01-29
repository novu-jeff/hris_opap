<?php

namespace App\Livewire\Employee\Loan;

use App\Models\EmployeeAccount;
use App\Notifications\Notifications;
use Livewire\Component;
use App\Models\Loan;
use App\Models\LoanType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanApplication extends Component
{
    public $loan_type_id;
    public $principal_amount;
    public $term_months;

    public $employee_no;
    public $employee_id;
    public $loanTypes;

    public $loan_id = null; 
    public $loan; // <--- add this

    protected $listeners = ['save'];

    public function mount($loan_id = null)
    {
        $this->employee_no = Auth::user()->employee_no;
        $this->employee_id = Auth::user()->id;

        $this->loanTypes = LoanType::where('is_active', 1)->get();

        if ($loan_id) {
           
            $this->loan = Loan::where('id', $loan_id)
                ->where('employee_no', $this->employee_no)
                ->firstOrFail();

            // Only allow edit if pending
            if ($this->loan->status !== 'pending') {
                abort(403, 'This loan cannot be edited.');
            }

            // Populate fields with existing loan data
            $this->loan_type_id = $this->loan->loan_type_id;
            $this->principal_amount = $this->loan->principal_amount;
            $this->term_months = $this->loan->term_months;
            $this->loan_id = $loan_id;
        }
    }


    /* =========================
     |  Validation Rules
     ========================= */
    public function rules()
    {
        return [
            'loan_type_id' => 'required|exists:loan_types,id',
            'principal_amount' => 'required|numeric|min:100',
            'term_months' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'loan_type_id.required' => 'Loan type is required.',
            'loan_type_id.exists' => 'Invalid loan type selected.',
            'principal_amount.required' => 'Loan amount is required.',
            'principal_amount.min' => 'Minimum loan amount is 100.',
            'term_months.required' => 'Loan term is required.',
            'term_months.min' => 'Loan term must be at least 1 month.',
        ];
    }

    /* =========================
     |  Save (with confirmation)
     ========================= */
    public function save(bool $confirm = true)
{
    // Check existing pending loans
    $pending = Loan::where('employee_no', $this->employee_no)
        ->whereIn('status', ['pending', 'approved'])
        ->exists();

    if ($pending) {
        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Oops',
            'message' => 'You already have a pending or active loan.'
        ]);
    }



    $this->validate();

    if ($confirm) {
        return $this->dispatch('showConfirmation', [
            'title' => $this->loan_id ? 'Confirm Loan Update' : 'Confirm Loan Application',
            'message' => $this->loan_id
                ? 'Are you sure you want to update this loan?'
                : 'Are you sure all loan details are correct? This will be submitted for approval.',
            'action' => 'save'
        ]);
    }

    try {
        DB::beginTransaction();

        $monthly = round($this->principal_amount / $this->term_months, 2);

        if ($this->loan_id) {
            // Update existing loan
            $this->loan->update([
                'loan_type_id' => $this->loan_type_id,
                'principal_amount' => $this->principal_amount,
                'term_months' => $this->term_months,
                'monthly_amortization' => $monthly,
                'balance' => $this->principal_amount,
            ]);

            $message = 'Your loan application has been updated successfully.';

        } else {
            // Check existing pending loans
            $pending = Loan::where('employee_no', $this->employee_no)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($pending) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'You already have a pending or active loan.'
                ]);
            }

            // Create new loan
            Loan::create([
                'employee_no' => $this->employee_no,
                'loan_type_id' => $this->loan_type_id,
                'principal_amount' => $this->principal_amount,
                'term_months' => $this->term_months,
                'monthly_amortization' => $monthly,
                'balance' => $this->principal_amount,
                'status' => 'pending',
                'created_by' => $this->employee_id,
            ]);

            $message = 'Your loan application has been submitted for approval.';
        }

        DB::commit();

        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Success!',
            'message' => $message,
            'redirect' => '_reload'
        ]);

        $this->resetExcept('employee_no', 'employee_id', 'loanTypes');

    } catch (\Exception $e) {
        DB::rollBack();

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Error',
            'message' => $e->getMessage()
        ]);
    }
}


    public function render()
    {
        return view('livewire.employee.loan.loan-application');
    }
}
