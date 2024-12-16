<div class="card border-0 mt-3">
    <div class="card-body p-0" wire:ignore>
        <table class="table w-100 data-tables" wire:ignore>
            <thead>
                <tr>
                    <th>Shift Name</th>
                    <th>Working Shift</th>
                    <th>Break Time Hours</th>
                    <th>Work Setup</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr data-id="{{$record->id}}">
                        <td>{{$record->name}}</td>
                        <td>
                            @if($record->shift_duration !== 'flexible')
                                {{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($record->end_shift)->format('h:i A') }}
                            @else
                                <p class="text-muted fst-italic mb-0">Flexible 8 Hours</p>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($record->break_out)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($record->break_in)->format('h:i A') }}</td>
                        <td>{{$record->work_setup}}</td>
                        <td>
                            <a href="{{route('shift-schedule.edit', ['shift_schedule' => $record->id])}}" class="btn btn-primary mx-1">
                                <i class="fa-solid fa-edit"></i>
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
