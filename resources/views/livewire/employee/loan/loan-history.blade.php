<div class="card border-0 mt-3">
    <div class="card-body p-0">

        <!-- Filters -->
        <div class="row mb-3 mt-5">
            <div class="col-md-6 d-flex align-items-center gap-2">
                <label class="form-label mb-0">Show entries:</label>
                <select wire:model.change="entries" class="form-select w-auto">
                    @foreach([5,10,20,30,40,50,100] as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 d-flex justify-content-end align-items-center gap-2">
                <label class="form-label mb-0">Filter Status:</label>
                <select wire:model.change="status" class="form-select w-50 text-uppercase">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="released">Released</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date Applied</th>
                        <th>Loan Type</th>
                        <th>Amount</th>
                        <th>Term</th>
                        <th>Monthly</th>
                        <th>Balance</th>
                        @if($status === 'all')
                            <th>Status</th>
                        @endif
                        <th style="width: 120px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>#{{ format_id($record->id, 6) }}</td>
                            <td>{{ optional($record->created_at)->format('F d, Y') }}</td>
                            <td>{{ $record->loanType->name ?? '-' }}</td>
                            <td>₱{{ number_format($record->principal_amount, 2) }}</td>
                            <td>{{ $record->term_months }} mos</td>
                            <td>₱{{ number_format($record->monthly_amortization, 2) }}</td>
                            <td>₱{{ number_format($record->balance, 2) }}</td>

                            @if($status === 'all')
                                <td>{!! status_alert($record->status) !!}</td>
                            @endif

                            <td class="text-center">
                               

                                @if($record->status === 'pending')
                                    <button wire:click="CancelLoan(true, {{$record->id}})"
                                        class="btn btn-danger btn-sm mx-1"
                                        title="Cancel">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                                @endif

                                @if($record->status === 'pending')
                                    <a href="{{ route('employee.loan.edit', $record->id) }}"
                                    class="btn btn-primary btn-sm mx-1"
                                    title="Edit Loan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center fw-bold py-3">
                                No loan records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $records->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>



