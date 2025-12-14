<div>
    <!-- Modal for viewing loan details -->
    <div class="modal fade" wire:ignore.self id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">View Loan Application</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($viewLoan)
                        <div class="row">
                            <div class="col-12 col-md-4 mb-4">
                                <label class="mb-2">Employee No.</label>
                                <input type="text" class="form-control restricted" value="{{ $viewLoan->employee_no }}" readonly>
                            </div>
                            <div class="col-12 col-md-4 mb-4">
                                <label class="mb-2">Employee Name</label>
                                <input type="text" class="form-control restricted" value="{{ optional($viewLoan->personal)->firstname ?? '-' }} {{ optional($viewLoan->personal)->lastname ?? '-' }}" readonly>
                            </div>
                            <div class="col-12 col-md-4 mb-4">
                                <label class="mb-2">Date Applied</label>
                                <input type="date" class="form-control restricted" value="{{ $viewLoan->created_at->format('Y-m-d') }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2">Loan Type</label>
                                <input type="text" class="form-control restricted" value="{{ $viewLoan->loanType->name ?? '-' }}" readonly>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2">Principal Amount</label>
                                <input type="text" class="form-control restricted" value="₱{{ number_format($viewLoan->principal_amount, 2) }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2">Term (Months)</label>
                                <input type="text" class="form-control restricted" value="{{ $viewLoan->term_months }}" readonly>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2">Status</label>
                                <input type="text" class="form-control restricted" value="{{ strtoupper($viewLoan->status) }}" readonly>
                            </div>
                        </div>

                        @if($viewLoan->status === 'pending')
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label class="mb-2">Reason for Disapproval</label>
                                <textarea class="form-control" rows="3" wire:model.defer="disapproval_note"></textarea>
                            </div>
                        </div>
                        @elseif($viewLoan->status === 'disapproved')
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label class="mb-2">Reason for Disapproval</label>
                                <textarea class="form-control restricted" rows="3" readonly>{{ $viewLoan->disapproval_note }}</textarea>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                @if($viewLoan?->status === 'pending')
                    <div class="modal-footer">
                        <button wire:click="disapproved" class="btn btn-danger">Disapprove</button>

                        <button wire:click="approve({{ $viewLoan->id }})" class="btn btn-primary text-uppercase fw-medium">Approve</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loan Applications Table -->
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="row mb-4">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label for="entries" class="form-label mb-0">Show entries:</label>
                    <select id="entries" wire:model.live="entries" class="form-select w-auto">
                        @foreach([5,10,20,30,50,100] as $val)
                            <option value="{{ $val }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                    <label for="search" class="form-label mb-0">Search:</label>
                    <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search by name or employee no">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>Employee No.</th>
                            <th>Employee Name</th>
                            <th>Loan Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr>
                                <td>{{ $loan->employee_no }}</td>
                                <td>{{ optional($loan->personal)->firstname ?? '-' }} {{ optional($loan->personal)->lastname ?? '-' }}</td>
                                <td>{{ $loan->loanType->name ?? '-' }}</td>
                                <td>₱{{ number_format($loan->principal_amount, 2) }}</td>
                                <td>{{ strtoupper($loan->status) }}</td>
                                <td>
                                    <button wire:click="view({{ $loan->id }})" class="btn btn-primary btn-sm mx-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @if($loan->status === 'pending')
                                        <button wire:click="approve({{ $loan->id }})" class="btn btn-success btn-sm mx-1">Approve</button>
                                        <button wire:click="disapprove({{ $loan->id }})" class="btn btn-danger btn-sm mx-1">Disapprove</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center fw-bold py-3">No loan applications found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('show-loan-modal', event => {
        var modal = new bootstrap.Modal(document.getElementById('showModal'));
        modal.show();
    });
</script>


