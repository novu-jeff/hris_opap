<div>
    <div class="d-flex justify-content-between mb-5">
        <a href="{{ route('timekeeping.index', ['year' => $records['previous']['year'], 'month' => $records['previous']['month']]) }}" class="btn btn-info px-5 py-3 text-uppercase fw-bold">
            Previous Month
        </a>
        <a href="{{ route('timekeeping.index', ['year' => $records['next']['year'], 'month' => $records['next']['month']]) }}" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
            Next Month
        </a>
    </div>
    <table class="table table-striped w-100">
        <thead>
            <tr>
                <th>Employee No</th>
                <th>Employee Name</th>
                <th>BSD No.</th>
                <th>Clock In</th>
                <th>Break Out</th>
                <th>Break In</th>
                <th>Clock Out</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records['data'] as $item)
            <tr>
                <td></td>
                <td></td>
                <td>{{$item->bsdno}}</td>
                <td>{{$item->clock_in_am}}</td>
                <td>{{$item->clock_out_am}}</td>
                <td>{{$item->clock_in_pm}}</td>
                <td>{{$item->clock_out_pm}}</td>
            </tr>
            @empty
               
            @endforelse
        </tbody>
    </table>
</div>
