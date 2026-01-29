<div>
    <div class="row" wire:poll>
        @forelse ($batches as $batch)
            @php
                $processedJobs = $batch->total_jobs - $batch->pending_jobs;
                $progress = $batch->total_jobs > 0 ? round(($processedJobs / $batch->total_jobs) * 100) : 0;
            @endphp

            <div class="col-12 col-md-6">
                <div class="card mb-4 shadow border-3 {{ $batch_id == $batch->id ? 'border-primary' : '' }}">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-2 text-uppercase fw-bold">{{ $batch->name ?? 'Unnamed Batch' }}</h5>
                                <p class="mb-0 text-muted d-block text-uppercase">Batch ID: {{ $batch->id }}</p>
                                <p class="mb-0 text-muted d-block text-uppercase">Action By: <strong>{{ $batch->action_by }}</strong></p>
                            </div>
                            <div class="text-uppercase" style="position: absolute; top: 20px; right: 20px;">
                                @if ($batch->cancelled_at)
                                    <span class="fw-bold px-3 py-2 badge bg-danger">Cancelled</span>
                                @elseif ($batch->has_failures)
                                    <span class="fw-bold px-3 py-2 badge bg-danger">Failed</span>
                                @elseif ($batch->finished_at)
                                    <span class="fw-bold px-3 py-2 badge bg-success">Completed</span>
                                @else
                                    <span class="fw-bold px-3 py-2 badge bg-info">In Progress</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-uppercase fw-bold small text-muted mb-1">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="row small text-muted text-uppercase mt-4">
                            <div class="col-12 col-md-6 mb-2">
                                <div class="d-flex gap-4">
                                    <div>
                                        <div><strong>Total Jobs:</strong> <span class="fw-bold text-decoration-underline">{{ $batch->total_jobs }}</span></div>
                                        <div><strong>Processed:</strong> <span class="fw-bold text-decoration-underline">{{ $processedJobs }}</span></div>
                                    </div>
                                    <div>
                                        <div><strong>Pending:</strong> <span class="fw-bold text-decoration-underline">{{ $batch->pending_jobs }}</span></div>
                                        <div><strong>Failed:</strong> <span class="fw-bold text-decoration-underline">{{ $batch->failed_jobs }}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <div><strong>Created:</strong> <span class="fw-bold text-decoration-underline">{{ $batch->created_at }}</span></div>
                                <div><strong>Finished:</strong> <span class="fw-bold text-decoration-underline">{{ $batch->finished_at ?? '-' }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-danger text-center text-uppercase mt-5" role="alert">
                    No job batches found.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 w-100">
        {{ $batches->links() }}
    </div>

    @if (!empty($batch_id))
        <div class="d-flex justify-content-center mt-3">
            <!-- <a href="{{ route('system.jobs') }}" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Go Back</a> -->
        </div>
    @endif
</div>
