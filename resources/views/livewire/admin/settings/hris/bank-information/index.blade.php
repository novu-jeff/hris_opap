<div class="card border-0 mt-3">
    <div class="card-body p-0" wire:ignore>
        <table class="table w-100">
            <thead>
                <tr>
                    <th>Bank Name</th>
                    <th>Account Number</th>
                    <th>Department</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                <tr data-id="{{$record->id}}">
                    <td>{{$record->name}}</td>
                        <td>{{$record->account_number}}</td>
                        <td>{{$record->departments->name}}</td>
                        <td>
                            <a href="{{route('bank-information.edit', ['bank_information' => $record->id])}}" class="btn btn-primary mx-1">
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