<div>

   <div class="d-flex justify-content-end align-items-center">
        <button class="btn btn-primary px-5 py-3">
            <i class="fa-solid fa-print me-2"></i> PRINT
        </button>
    </div>


    <div class="table-responsive mt-4 mb-3">
        <table class="table table-bordered mb-0 text-center align-middle" style="min-width: 820px;">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Employees</th>
                    <th>Employee (EE) Share</th>
                    <th>Employer (ER) Share</th>
                    <th>EC (ER Only)</th>
                    <th>Total Contributions</th>
                </tr>
            </thead>
            <tbody class="table-light">
                <tr>
                    <td>{{ $employee_count }}</td>
                    <td>₱ {{ number_format($total_employee_share, 2) }}</td>
                    <td>₱ {{ number_format($total_employer_share, 2) }}</td>
                    <td>₱ {{ number_format($total_ec, 2) }}</td>
                    <td class="fw-bold bg-success bg-opacity-25">₱ {{ number_format($total_contribution, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2 align-items-center">
            <div class="text-end d-flex justify-content-b align-items-center gap-2">
                <label for="year" class="form-label mb-0">Year:</label>
                <select id="year" wire:model.live="year" class="form-select">
                    @for ($y = now()->year; $y >= 2010; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="perPage" class="form-label mb-0">Show:</label>
                    <select id="perPage" wire:model.live="entries" class="form-select w-auto">
                        <option value="10">10</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="999999">All</option>
                    </select>
                </div>
        </div>
       
       <div>
            <div class="text-end d-flex justify-content-b align-items-center gap-2">
                <label for="search" class="form-label mb-0">Search:</label>
                <input id="search" wire:model.live="search" type="text" class="form-control w-100" placeholder="Search something...">
                <button class="btn btn-primary">Print</button>
            </div>
        </div>
    </div> --}}

    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th class="text-start bg-primary bg-opacity-25">No.</th>
                    <th class="text-start bg-primary bg-opacity-25">SS No.</th>
                    <th class="text-start bg-primary bg-opacity-25">Name</th>
                    <th class="text-start bg-primary bg-opacity-25">ER</th>
                    <th class="text-start bg-primary bg-opacity-25">EE</th>
                    <th class="text-start bg-primary bg-opacity-25">Total</th>
                    <th class="text-start bg-primary bg-opacity-25">EC</th>
                    <th class="text-start bg-primary bg-opacity-25">Monthly Salary</th>
                    <th class="text-start bg-primary bg-opacity-25">Status</th>
                </tr>
            </thead>                
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $record->personal->sss_no }}</td>
                        <td class="text-start">
                            {{ trim("{$record->personal->suffix} {$record->personal->firstname} {$record->personal->middlename} {$record->personal->lastname}") }}
                        </td>
                        <td class="text-start">₱ {{ $record->employee_share }}</td>
                        <td class="text-start">₱ {{ $record->employer_share }}</td>
                        <td class="text-start">₱ {{ $record->total }}</td>
                        <td class="text-start">₱ {{ $record->ec }}</td>
                        <td class="text-start">₱ {{ number_format($record->monthly_rate, 2) }}</td>
                        <td class="text-start">{{ $record->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center fw-bold py-3">No data was found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
</div>
