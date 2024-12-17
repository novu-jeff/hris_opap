@section('style')
@endsection
<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <table class="table data-tables w-100">
                <thead>
                    <tr>
                        <th>Employee #</th>
                        <th>Name</th>
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>{{$record->employee_no}}</td>
                            <td>
                                {{ $record->personal->firstname }}
                                {{ $record->personal->middlename ? substr($record->personal->middlename, 0, 1) . '.' : '' }}
                                {{ $record->personal->lastname }}
                            </td>                        
                            <td> 
                                <a target="_blank" href="{{ route('dtr.show', ['id' => $record->employee_no, 'date' => $date]) }}" class="btn btn-primary">
                                    <i class="fa-solid fa-eye"></i> Show DTR
                                </a>
                            </td>                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
