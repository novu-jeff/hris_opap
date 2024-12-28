<div>
    
    <div class="modal fade" wire:ignore.self id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">View Leave Application</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="employee_no">Employee No.</label>
                            <input type="text" id="employee_no" class="form-control restricted" value="{{ isset($view_records->employee) ? ($view_records->employee->employee_no) : '' }}" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="employee_name">Employee Name</label>
                            <input type="text" id="employee_name" class="form-control restricted" value="{{ isset($view_records->employee) ? $view_records->employee->firstname . ' ' . $view_records->employee->lastname : '' }}" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="employee_credit">Remaining Leave Credit</label>
                            <input type="text" id="employee_credit" class="form-control restricted" value="{{ isset($view_records->employment) ?  $view_records->employment->leave_credits : 0 }}" readonly>
                        </div>
                        <div class="col-12 mb-4">
                            <hr>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="type">Leave Type</label>
                            <input type="text" id="type" class="form-control restricted" value="{{$view_records->leave_type->name ?? ''}}" readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="duration">Duration</label>
                            <input type="text" id="duration" class="form-control restricted" value="{{ isset($view_records) && $view_records->to === null ? 'One Day' : 'Two or More Days' }}" readonly>
                        </div>
                        <div class="col-12 {{isset($view_records->to) ? 'col-md-6' : 'col-md-12'}} mb-4">
                            <label class="mb-2" for="from">{{isset($view_records->to) ? 'From' : 'Leave Date'}}</label>
                            <input type="text" id="from" class="form-control restricted" value="{{format_date($view_records->from ?? '', 'day_date_string')}}" readonly>
                        </div>
                        @if(isset($view_records->to))
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="to">To</label>
                                <input type="text" id="to" class="form-control restricted" value="{{format_date($view_records->to ?? '', 'day_date_string')}}" readonly>
                            </div>
                        @endif
                        <div class="col-12 col-md-12'}} mb-4">
                            <label class="mb-2" for="date_applied">Date Applied</label>
                            <input type="text" id="date_applied" class="form-control restricted" value="{{ isset($view_records->created_at) ? format_date($view_records->created_at, 'date_string')  : '' }}" readonly>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="reason">Reason</label>
                            <textarea id="reason" cols="30" rows="5" class="form-control restricted" readonly placeholder="Write something...">{{$view_records['reason'] ?? ''}}</textarea>
                        </div>
                    </div>
                </div>
                @if (isset($view_records->status) && $view_records->status === 'pending')
                    <div class="modal-footer">
                        <button wire:click="rejected" class="btn btn-danger text-uppercase fw-medium">Reject</button>
                        <button wire:click="granted" class="btn btn-primary text-uppercase fw-medium">Approve</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.leave', ['status' => 'pending'])}}" class="nav-link text-uppercase fw-medium {{$status === 'pending' ? 'active' : ''}}"  role="tab" aria-controls="pills-home" aria-selected="true">Pending</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.leave', ['status' => 'granted'])}}" class="nav-link text-uppercase fw-medium {{$status === 'granted' ? 'active' : ''}}" role="tab" aria-controls="pills-profile" aria-selected="false">Granted</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.leave', ['status' => 'rejected'])}}" class="nav-link text-uppercase fw-medium {{$status === 'rejected' ? 'active' : ''}}" role="tab" aria-controls="pills-profile" aria-selected="false">Rejected</a>
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
                                    <th>Employee No</th>
                                    <th>Employee Name</th>
                                    <th>Date Applied</th>
                                    <th style="max-width: 200px;">Action</th>
                                </tr>
                            </thead>                
                            <tbody>
                                @forelse($records as $record)
                                    <tr data-id="{{$record->id}}">
                                        <td>{{$record->employee_no}}</td>
                                        <td>{{$record->employee->firstname . ' ' . $record->employee->lastname}}</td>
                                        <td>{{format_date($record->created_at, 'date_string')}}</td>
                                        <td>
                                            <button type="button" wire:click="view({{$record->id}})" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
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

