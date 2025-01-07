<div class="card border-0 mt-3">
    <div class="card-body p-0">
        <div class="row mb-3 mt-5">
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
                <label for="search" class="form-label mb-0">Filter Status:</label>
                <select wire:model.change="status" id="status" class="form-select w-50">
                    <option value=""> - ALL - </option>
                    <option value="pending"> Pending </option>
                    <option value="granted"> Granted </option>
                    <option value="rejected"> Rejected </option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    @forelse($records as $record)
                        <tr data-id="{{$record->id}}">
                            <td>#{{format_id($record->id, 6)}}</td>
                            <td>{{'(' . $record->leave_type->code . ') - ' . $record->leave_type->name}}</td>
                            <td>{{format_date($record->from, 'date_string') . ' - ' . format_date($record->to, 'date_string')}}</td>
                            <td>
                                @if ($record->status == 'granted')
                                    <div class="alert alert-success fw-bold text-uppercase text-center fw-medium mb-0">Leave Granted</div>
                                @elseif ($record->status == 'rejected')
                                    <div class="alert alert-danger fw-bold text-uppercase text-center fw-medium mb-0">Leave Denied</div>
                                @elseif($record->status === 'pending')
                                    <div class="alert alert-info fw-bold text-uppercase text-center fw-medium mb-0">Pending</div>
                                @endif
                            </td>
                            <td>
                                @if($record->status === 'pending')
                                    <a href="{{route('employee.leave.edit', ['id' => $record->id])}}" class="btn btn-primary mx-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                        </tr> 
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $records->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>