<div class="container mt-3">

    <h4>User Trail Logs</h4>

    <div class="card">
        <div class="card-body">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Size (KB)</th>
                        <th>Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($trails as $trail)
                        <tr>
                            <td>{{ $trail['filename'] }}</td>
                            <td>{{ $trail['size'] }}</td>
                            <td>{{ $trail['updated_at'] }}</td>
                            <td>
                                <button wire:click="viewLog('{{ $trail['filename'] }}')" class="btn btn-primary btn-sm">
                                    View
                                </button>
                                <button wire:click="downloadLog('{{ $trail['filename'] }}')" class="btn btn-success btn-sm">
                                    Download
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div>
                {{ $trails->links() }}
            </div>

        </div>
    </div>
</div>
