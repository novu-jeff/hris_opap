<div> <!-- single root wrapper for Livewire -->
    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2 mb-md-0">
            <label for="cut_off_period" class="form-label mb-0">Cut-off Period</label>
            <select id="cut_off_period" wire:model.change="cutoffPeriod" class="form-select">
                <option value="">All</option>
                @foreach($cutOffPeriods as $period)
                    <option value="{{ $period }}">{{ $period }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-2 mb-md-0">
            <label class="fw-bold">Filter by Employment Type</label>
            <select wire:model.live="filterEmploymentType" class="form-select">
                <option value="">All</option>
                @foreach($employmentTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Second row: Show entries & Search input -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-3 mb-2 mb-md-0 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.live="entries" class="form-select w-auto">
                @foreach([5,10,20,30,40,50,60,70,80,90,100] as $num)
                    <option value="{{ $num }}">{{ $num }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 offset-md-5 mt-2 mt-md-0">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Search Payroll ID or Type">
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Payroll ID</th>
                    <th>Employement Type</th>
                    <th>Cut-off Period</th>
                    <th>Payroll Date</th>
                    <th>No. of Employees</th>  
                    <th>Status</th>               
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $payroll)
                    <tr>
                        <td>{{ $payroll->id }}</td>
                        <td
                        @php
                            $typeColors = [
                                'Contractual' => 'bg-primary text-white fw-bold',         // yellow
                                'Contract of Service' => 'bg-info text-white fw-bold',   // blue
                                'Job Order' => 'bg-danger text-white fw-bold',          // red
                            ];

                            $types = explode(', ', $payroll->employment_types ?? '');
                            $classes = collect($types)->map(function($type) use ($typeColors) {
                                return $typeColors[$type] ?? '';
                            })->filter()->implode(' '); // combine classes if multiple types
                        @endphp
                        class="{{ $classes }}"
                    >
                        {{ $payroll->employment_types ?? '-' }}
                    </td>
                        <td>{{ $payroll->cut_off_period }}</td>
                        <td>{{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M j, Y') }}</td>
                        <td>{{ $payroll->items_count }}</td>
                        <td>
                        @if($payroll->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($payroll->status === 'disapproved')
                            <span class="badge bg-danger">Disapproved</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </td>
                        <td>
                            <div class="d-flex gap-2">
                                @if($payroll->status !== 'pending')
                                <a target="_blank"
                                    href="{{ route('reports.payroll.view', $payroll->id) }}"
                                    class="btn btn-primary btn-sm"
                                    title="View Payroll">
                                        <i class="fa-regular fa-folder-open"></i>
                                </a>
                                 @endif
                                {{-- APPROVE --}}
                                @if($payroll->status !== 'approved')
                                    <button
                                        wire:click="approve({{ $payroll->id }})"
                                        class="btn btn-success btn-sm"
                                        title="Approve Payroll">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @endif

                                {{-- DISAPPROVE --}}
                                @if($payroll->status !== 'pending')
                                    <button
                                        wire:click="disapprove({{ $payroll->id }})"
                                        class="btn btn-warning btn-sm"
                                        title="Pending Payroll">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                @endif
                                @if($payroll->status !== 'pending')
                                <button
                                    wire:click="downloadPayroll({{ $payroll->id }})"
                                    class="btn btn-info btn-sm"
                                    title="Download Payroll"
                                >
                                    <i class="fa-solid fa-download"></i>
                                </button>
                                 @endif
                                @if($payroll->status !== 'approved')
                                    <button wire:click="remove(true, {{ $payroll->id }})" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
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
