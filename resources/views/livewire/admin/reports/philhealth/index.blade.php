<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
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
    </div>
   
    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>PIN</th>
                    <th>Lastname</th>
                    <th>Suffix</th>
                    <th>Firstname</th>
                    <th>Middlename</th>
                    <th>Birthday</th>
                    <th>PhilHealth Total</th>
                    <th>ER Share</th>
                    <th>EE Share</th>
                    <th>Monthly Salary</th>
                </tr>
            </thead>                
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $record->personal->philhealth_no }}</td>
                        <td>{{ $record->personal->lastname }}</td>
                        <td>{{ $record->personal->suffix ?? 'N/A' }}</td>
                        <td>{{ $record->personal->firstname }}</td>
                        <td>{{ $record->personal->middlename }}</td>
                        <td>{{ $record->personal->birthday }}</td>
                        <td>₱{{ $record->total }}</td>
                        <td>₱{{ $record->employee_share }}</td>
                        <td>₱{{ $record->employer_share }}</td>
                        <td>₱ {{ number_format($record->monthly_rate, 2) }}</td>
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
