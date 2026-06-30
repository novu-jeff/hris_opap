<div class="card border-0 mt-3">
    <div class="card-body p-0">
        <div class="row mb-4">

            <div class="col-md-3">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Earned Hours</h6>
                        <h2>{{ number_format($earnedHours,2) }}</h2>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Used Hours</h6>
                        <h2>{{ number_format($usedHours,2) }}</h2>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Available Offset</h6>
                        <h2 class="text-primary">
                            {{ number_format($remainingHours,2) }}
                        </h2>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-center">
        
                        @if($remainingHours > 0)

                            <a href="{{ route('employee.offset.apply') }}"
                            class="btn btn-primary btn-lg w-100 py-3">
                                <i class="fa-solid fa-plus me-2"></i>
                                Apply Offset
                            </a>

                        @else

                            <button
                                class="btn btn-secondary btn-lg w-100 py-3"
                                disabled>

                                <i class="fa-solid fa-ban me-2"></i>
                                No Available Offset

                            </button>

                        @endif
        
                    </div>
                </div>
            </div>
        
        </div>
        <div class="row mb-3 mt-5">
            <div class="col-md-6 d-flex align-items-center gap-2">
                <label for="entries" class="form-label mb-0">Show entries:</label>
                <select id="entries" wire:model.change="entries" class="form-select w-auto">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="70">70</option>
                    <option value="80">80</option>
                    <option value="90">90</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                <label for="search" class="form-label mb-0">Filter Status:</label>
                <select wire:model.change="status" id="status" class="form-select w-50 text-uppercase">
                    <option value="all">All</option>
                    <option value="pending"> Pending </option>
                    <option value="granted"> Granted </option>
                    <option value="disapproved"> Disapproved </option>
                    <option value="cancelled"> Cancelled </option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Office Order No.</th>
                        <th>Date Filed</th>
                        <th>Date Covered</th>
                        <th>Hours</th>
        
                        @if($status == 'all')
                            <th>Status</th>
                        @endif
        
                        <th width="180">Action</th>
                    </tr>
                </thead>
        
                <tbody>
        
                    @forelse($records as $record)
        
                        <tr>
        
                            <td>#{{ format_id($record->id,6) }}</td>
        
                            <td>{{ $record->office_order_no }}</td>
        
                            <td>
                                {{ \Carbon\Carbon::parse($record->filing_date)->format('M d, Y') }}
                            </td>
        
                            <td>
                                {{ \Carbon\Carbon::parse($record->date_from)->format('M d, Y') }}
                                -
                                {{ \Carbon\Carbon::parse($record->date_to)->format('M d, Y') }}
                            </td>
        
                            <td>{{ number_format($record->hours_requested,2) }}</td>
        
                            @if($status == 'all')
                                <td>{!! status_alert($record->status) !!}</td>
                            @endif
        
                            <td>
        
                                @if($record->status=='pending')
        
                                    <a href="{{ route('employee.offset.edit',$record->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
        
                                    <button
                                        wire:click="cancel(true,{{$record->id}})"
                                        class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
        
                                @endif
        
                                @if($record->status=='approved')
        
                                  <!--  <button
                                        wire:click="download({{$record->id}})"
                                        class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-download"></i>
                                    </button> -->
        
                                @endif
        
                                @if($record->status=='disapproved')
        
                                    <a href="{{ route('employee.offset.edit',$record->id) }}"
                                       class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
        
                                @endif
        
                            </td>
        
                        </tr>
        
                    @empty
        
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No offset applications found.
                            </td>
                        </tr>
        
                    @endforelse
        
                </tbody>
            </table>
        </div>
    </div>
</div>