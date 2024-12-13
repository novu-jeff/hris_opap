<div class="card border-0 mt-3">
    <div class="card-body p-0" wire:ignore>
        <table class="table data-tables w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time Span</th>
                    <th>Status</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr data-id="{{$record->id}}">
                        <td>#{{format_id($record->id, 6)}}</td>
                        <td>{{format_date($record->date, 'day_date_string')}}</td>
                        <td>{{format_time($record->start_time) . ' - ' . format_time($record->end_time)}}</td>
                        <td>
                            @if ($record->status == 'approve')
                                <div class="alert alert-success fw-bold text-uppercase text-center fw-medium mb-0">Leave Granted</div>
                            @elseif ($record->status == 'rejected')
                                <div class="alert alert-danger fw-bold text-uppercase text-center fw-medium mb-0">Leave Denied</div>
                            @elseif($record->status === 'pending')
                                <div class="alert alert-info fw-bold text-uppercase text-center fw-medium mb-0">Pending</div>
                            @endif
                        </td>
                        <td>
                            @if($record->status === 'pending')
                                <a href="{{route('employee.atro.edit', ['id' => $record->id])}}" class="btn btn-primary mx-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            @endif
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