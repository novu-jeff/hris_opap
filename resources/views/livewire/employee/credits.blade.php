<div>
    <div class="row">
        @forelse ($records as $record)
            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-uppercase fw-bold py-3">{{$record->leave->code . ' - ' . $record->leave->name}}</h5>
                    </div>
                    <div class="card-body">
                        <h1>{{$record->credits}}</h1>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-primary text-center fw-bold text-uppercase">No Credits Left</div>
            </div>
        @endforelse
    </div>
</div>
