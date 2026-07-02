<div>
    <div class="modal fade"
     wire:ignore.self
     id="showModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Offset Application Details
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            @if($view_records)

            <div class="modal-body">

                {{-- Employee Information --}}
                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label>Employee No.</label>

                        <input
                            class="form-control"
                            value="{{ $view_records->employee_no }}"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Employee Name</label>

                        <input
                            class="form-control"
                            value="{{ $view_records->employee?->personal?->firstname }} {{ $view_records->employee?->personal?->lastname }}"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Status</label>

                        <input
                            class="form-control"
                            value="{{ ucfirst($view_records->status) }}"
                            readonly>

                    </div>

                </div>

                {{-- Dates --}}
                <div class="row">

                    <div class="col-md-4 mb-3">
                
                        <label>Date Filed</label>
                
                        <input
                            class="form-control"
                            value="{{ format_date($view_records->filing_date,'date_string') }}"
                            readonly>
                
                    </div>
                
                    <div class="col-md-4 mb-3">
                
                        <label>Offset Date</label>
                
                        <input
                            class="form-control"
                            value="{{ format_date($view_records->offset_date,'date_string') }}"
                            readonly>
                
                    </div>
                
                    <div class="col-md-4 mb-3">
                
                        <label>Request Type</label>
                
                        <input
                            class="form-control"
                            value="
                                @switch($view_records->request_type)
                                    @case('AM') AM Half Day @break
                                    @case('PM') PM Half Day @break
                                    @case('WHOLE_DAY') Whole Day @break
                                @endswitch
                            "
                            readonly>
                
                    </div>
                
                </div>

                {{-- Offset Details --}}
                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Office Order No.</label>

                        <input
                            class="form-control"
                            value="{{ $view_records->office_order_no ?: 'Will be generated upon approval' }}"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Hours Requested</label>

                        <input
                            class="form-control"
                            value="{{ number_format($view_records->hours_requested,2) }}"
                            readonly>

                    </div>

                </div>

                {{-- Reason --}}
                <div class="mb-3">

                    <label>Reason</label>

                    <textarea
                        rows="5"
                        class="form-control"
                        readonly>{{ $view_records->reason }}</textarea>

                </div>

                {{-- Attachments --}}
                @if($view_records->attachments->count())

                    <div class="mb-3">

                        <label>Attachments</label>

                        <ul class="list-group">

                            @foreach($view_records->attachments as $attachment)

                                <li class="list-group-item">

                                    <a
                                        href="{{ Storage::url($attachment->attachment) }}"
                                        target="_blank">

                                        {{ basename($attachment->attachment) }}

                                    </a>

                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                @if(
                $view_records->status == 'pending' ||
                ($view_records->status == 'approved' && $isEdit)
            )

            <div class="mb-3">

                <label class="form-label">
                    Remarks / Reason for Disapproval
                    <span class="text-danger">*</span>
                </label>

                <textarea
                    class="form-control"
                    rows="4"
                    wire:model.defer="remarks"
                    placeholder="Enter the reason for disapproval..."></textarea>

                @error('remarks')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

            </div>

            @endif

            </div>

            @if($view_records->status == 'pending')

            <div class="modal-footer">

                <button
                    wire:click="disapproved"
                    class="btn btn-danger">

                    Disapprove

                </button>

                <button
                    wire:click="approved"
                    class="btn btn-success">

                    Approve

                </button>

            </div>

            @endif
            @if($view_records->status == 'disapproved')

                <button
                    wire:click="approved"
                    class="btn btn-success">

                    <i class="fa-solid fa-check me-1"></i>

                    Approve

                </button>

            @endif

            @if($view_records->status == 'approved')

                <button
                    wire:click="changeToDisapproved"
                    class="btn btn-danger">

                    <i class="fa-solid fa-arrow-rotate-left me-1"></i>

                    Change to Disapproved

                </button>

            @endif

            @endif

        </div>

    </div>

</div>


    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.offset', ['status' => 'pending'])}}" class="nav-link text-uppercase fw-medium {{$status === 'pending' ? 'active' : ''}}"  role="tab" aria-controls="pills-home" aria-selected="true">Pending</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.offset', ['status' => 'granted'])}}" class="nav-link text-uppercase fw-medium {{$status === 'granted' ? 'active' : ''}}" role="tab" aria-controls="pills-profile" aria-selected="false">Granted</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.offset', ['status' => 'disapproved'])}}" class="nav-link text-uppercase fw-medium {{$status === 'disapproved' ? 'active' : ''}}" role="tab" aria-controls="pills-profile" aria-selected="false">Disapproved</a>
                </li>
            </ul>
            <div class="tab-content mt-5" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex align-items-center gap-2">
                            <label for="entries" class="form-label mb-0">Show entries:</label>
                            <select id="entries" wire:model.live="entries" class="form-select w-auto">
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
                            <label for="search" class="form-label mb-0">Search:</label>
                            <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Employee No.</th>
                                    <th>Employee Name</th>
                                    <th>Office Order No.</th>
                                    <th>Offset Date</th>
                                    <th>Request Type</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                    <th width="180">Action</th>
                                </tr>
                                </thead>
                            
                            <tbody>
                            @forelse($records as $record)
                            
                            <tr>
                            
                                <td>{{ $record->employee_no }}</td>
                            
                                <td>
                                    {{ $record->employee->personal->firstname }}
                                    {{ $record->employee->personal->lastname }}
                                </td>
                            
                                <td>
                                    @if($record->status == 'disapproved')
                                
                                        <span class="badge bg-danger">
                                            N/A
                                        </span>
                                
                                    @elseif($record->office_order_no)
                                
                                        {{ $record->office_order_no }}
                                
                                    @else
                                
                                        <span class="badge bg-secondary">
                                            Pending
                                        </span>
                                
                                    @endif
                                </td>
                                
                                <td>{{ format_date($record->offset_date,'date_string') }}</td>
                                
                                <td>
                                    @switch($record->request_type)
                                        @case('AM')
                                            <span class="badge bg-info">
                                                AM Half Day
                                            </span>
                                            @break
                                
                                        @case('PM')
                                            <span class="badge bg-warning text-dark">
                                                PM Half Day
                                            </span>
                                            @break
                                
                                        @case('WHOLE_DAY')
                                            <span class="badge bg-success">
                                                Whole Day
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                
                                <td>{{ number_format($record->hours_requested,2) }}</td>
                            
                                <td>{!! status_alert($record->status) !!}</td>
                            
                                <td>

                                    @if($record->status == 'approved')

                                        <button
                                            class="btn btn-warning btn-sm"
                                            wire:click="view({{ $record->id }}, true)"
                                            title="Correct Approval">
                                
                                            <i class="fa-solid fa-pen-to-square"></i>
                                
                                        </button>
                                
                                    @endif
                            
                                    <button
                                        class="btn btn-primary"
                                        wire:click="view({{ $record->id }})">
                            
                                        <i class="fa-solid fa-eye"></i>
                            
                                    </button>
                            
                                    <button
                                        class="btn btn-danger"
                                        wire:click="remove(true,{{ $record->id }})">
                            
                                        <i class="fa-solid fa-trash"></i>
                            
                                    </button>
                            
                                </td>
                            
                            </tr>
                            
                            @empty
                            
                            <tr>
                            
                                <td colspan="7" class="text-center">
                            
                                    No offset applications found.
                            
                                </td>
                            
                            </tr>
                            
                            @endforelse
                            
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $records->links(data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>