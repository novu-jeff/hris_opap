<div class="card border-0 mt-3">
    <div class="card-body p-0" wire:ignore>
        <table class="table w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr data-id="{{$record->id}}">
                        <td>#{{format_id($record->id, 6)}}</td>
                        <td>
                            @if ($record->status == 'approved')
                                <div class="alert alert-success text-uppercase text-center fw-medium mb-0">Leave Granted</div>
                            @elseif ($record->status == 'rejected')
                                <div class="alert alert-danger text-uppercase text-center fw-medium mb-0">Leave Denied</div>
                            @elseif($record->status === 'pending')
                                <div class="alert alert-info text-uppercase text-center fw-medium mb-0">Waiting for approval</div>
                            @endif
                        </td>
                        <td>{{$record->type}}</td>
                        <td>{{format_date($record->from, 'date_string') . ' - ' . format_date($record->to, 'date_string')}}</td>
                        <td>
                            <a href="{{route('employee.leave.edit', ['id' => $record->id])}}" class="btn btn-primary mx-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
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