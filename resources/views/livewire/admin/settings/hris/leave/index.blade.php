<div class="card border-0 mt-3">
    <div class="card-body p-0" wire:ignore>
        <table class="table data-tables w-100">
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
                        <td>
                            <a href="{{route('leave.edit', ['leave' => $record->id])}}" class="btn btn-primary mx-1">
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