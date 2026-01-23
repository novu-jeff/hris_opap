<div>
    {{-- Filters --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-3">
            <label for="cut_off_period" class="form-label mb-0">Cut-off Period</label>
            <select id="cut_off_period" wire:model="cutoffPeriod" class="form-select">
                <option value="">All</option>
                @foreach($cutOffPeriods as $period)
                    <option value="{{ $period }}">{{ $period }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="salary_method" class="form-label mb-0">Salary Method</label>
           <select id="salary_method" wire:model="salaryMethod" class="form-select">
            <option value="">All</option>
            @foreach($salaryMethods as $method)
                <option value="{{ $method }}">{{ $method }}</option>
            @endforeach
        </select>
        </div>
        <div class="col-md-6 d-flex justify-content-end align-items-end gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.live="entries" class="form-select w-auto">
                @foreach([5,10,20,30,40,50,60,70,80,90,100] as $num)
                    <option value="{{ $num }}">{{ $num }}</option>
                @endforeach
            </select>

            <input type="text" wire:model.live="search" class="form-control w-50" placeholder="Search Payroll ID or Type">
        </div>
    </div>

    {{-- Payroll Table --}}
    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Payroll ID</th>
                    <th>Cut-off Period</th>
                    <th>Payroll Date</th>
                    <th>No. of Employees</th>
                    <th>Net Amount</th>
                    <th>Salary Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $payroll)
                    <tr>
                        <td>{{ $payroll->id }}</td>
                        <td>{{ $payroll->cut_off_period }}</td>
                        <td>{{ \Carbon\Carbon::parse($payroll->payroll_date)->format('Y-m-d') }}</td>
                        <td>{{ $payroll->items_count }}</td>
                        <td>{{ number_format($payroll->items_sum_net_amount ?? 0, 2) }}</td>
                        <td>{{ number_format($payroll->total_salary_amount ?? 0, 2) }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a target="_blank" href="#" class="btn btn-primary btn-sm" title="View Payroll">
                                    <i class="fa-regular fa-folder-open"></i>
                                </a>
                                <a target="_blank" href="#" class="btn btn-info btn-sm" title="Download Payroll">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <button wire:click="delete({{ $payroll->id }})" class="btn btn-danger btn-sm" title="Delete Payroll">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center fw-bold py-3">No payroll records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $payrolls->links() }}
        </div>

    </div>
</div>
