<div class="card border-0 mt-3">
    <div class="card-body p-0">
        <table class="table data-tables w-100">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr>
                        <td>{{$record->name}}</td>
                        <td>
                            @if ($record->isYearly)
                                {{ \Carbon\Carbon::parse($record->date)->format('F j') }}
                            @else
                                {{ \Carbon\Carbon::parse($record->date)->format('F j, Y') }}
                            @endif
                        </td>
                        <td>
                            <a href="{{route('holiday.edit', ['holiday' => $record->id])}}" class="btn btn-primary mx-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button class="btn btn-danger mx-1" wire:click="remove(true, {{ $record->id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>