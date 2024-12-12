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
                            <input type="text" id="employee_no" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->employee_no) : '' }}" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="employee_name">Employee Name</label>
                            <input type="text" id="employee_name" class="form-control restricted" value="{{ isset($view_records) ? $view_records->firstname . ' ' . $view_records->lastname : '' }}" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="date_filed">Date Filed</label>
                            <input type="date" id="date_filed" class="form-control restricted" value="{{ isset($view_records) ? $view_records->date_filed : '' }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-7 mb-4">
                            <label class="mb-2" for="section">Section</label>
                            <input type="text" id="section" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->section_name . ' (' . $view_records->section_code . ') ') : '' }}" readonly>
                        </div>
                        <div class="col-12 col-md-5 mb-4">
                            <label class="mb-2" for="department">Department</label>
                            <input type="text" id="department" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->department_name . ' (' . $view_records->department_code . ') ') : '' }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="branch">Position</label>
                            <input type="text" id="branch" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->branch_name . ' (' . $view_records->branch_code . ') ') : '' }}" readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="position">Position</label>
                            <input type="text" id="position" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->position_name . ' (' . $view_records->position_code . ') ') : '' }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="destination">Destination</label>
                            <input type="text" id="destination" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->destination) : ''}}" readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="purpose">Purpose</label>
                            <input type="text" id="purpose" class="form-control restricted" value="{{ isset($view_records) ? ($view_records->purpose) : ''}}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="arrival_time">Arrival Time</label>
                            <input type="text" id="arrival_time" class="form-control restricted" 
                                   value="{{ isset($view_records) ? \Carbon\Carbon::createFromFormat('H:i:s', $view_records->arrival_time)->format('g:i A') : '' }}" 
                                   readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="departure_time">Departure Time</label>
                            <input type="text" id="departure_time" class="form-control restricted" 
                                   value="{{ isset($view_records) ? \Carbon\Carbon::createFromFormat('H:i:s', $view_records->departure_time)->format('g:i A') : '' }}" 
                                   readonly>
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
        <div class="card-body p-0" wire:ignore>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.obs.index', ['status' => 'pending'])}}" class="nav-link text-uppercase fw-medium {{$status === 'pending' ? 'active' : ''}}"  role="tab" aria-controls="pills-home" aria-selected="true">Pending</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.obs.index', ['status' => 'granted'])}}" class="nav-link text-uppercase fw-medium {{$status === 'granted' ? 'active' : ''}}" role="tab" aria-controls="pills-profile" aria-selected="false">Granted</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('ess.obs.index', ['status' => 'rejected'])}}" class="nav-link text-uppercase fw-medium {{$status === 'rejected' ? 'active' : ''}}" role="tab" aria-controls="pills-profile" aria-selected="false">Rejected</a>
                </li>
            </ul>
            {{ $status }}
            <div class="tab-content mt-5" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <table class="table w-100" wire:ignore>
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Date Applied</th>
                                <th style="max-width: 200px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($records as $record)
                                <tr data-id="{{$record->id}}">
                                    <td>#{{format_id($record->id, 6)}}</td>
                                    <td>{{$record->firstname . ' ' . $record->lastname}}</td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div>