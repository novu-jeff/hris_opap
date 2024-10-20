<div class="card border-0 mt-3">
    <div class="card-body p-0">
        <table class="table w-100">
            <thead>
                <tr>
                    <th>Name</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr>
                        <td>{{$record->name}}</td>
                        <td>
                            <a href="{{route('violation.edit', ['violation' => $record->id])}}" class="btn btn-primary mx-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button class="btn btn-danger mx-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>