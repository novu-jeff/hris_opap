<div class="card border-0 mt-3">
    <div class="card-body p-0">
        <table class="table w-100">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @foreach($records as $record)
                    <tr>
                        <td>{{ $record['month'] }}</td>
                        <td>{{ $record['year'] }}</td>
                        <td>
                            <!-- Button with dynamic route generation including month and year -->
                            <a href="{{ route('dtr.index', ['month' => $record['month'], 'year' => $record['year']]) }}" class="btn btn-primary mx-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
