<div class="card border-0 mt-3">
    <div class="card-body p-0" wire:ignore>
        <table class="table w-100">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr data-id="{{$record->id}}">
                        <td>{{$record->code}}</td>
                        <td>{{$record->name}}</td>
                        <td class="d-flex justify-content-end">
                            @if ($record->source == 'entry')
                                <a href="{{route('deductions.index', ['id' => $record->id])}}" class="btn btn-secondary mx-1">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            @endif
                            <a href="{{route('other-deductions.edit', ['other_deduction' => $record->id])}}" class="btn btn-primary mx-1">
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