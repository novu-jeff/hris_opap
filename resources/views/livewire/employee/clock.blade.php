<div>
    <div class="clockinout">
        <div class="d-flex align-items-center justify-content-center gap-4">
            <div class="card border-3 border-primary {{!$isClockedIn ? 'bg-primary text-white' : ''}}" wire:click="clockin">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <div class="d-flex justify-content-center">
                            <i class="fa-regular fa-circle-check"></i>
                        </div>
                        <div class="text-center mt-3">
                            Clock In
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-3 border-danger {{!$isClockedOut ? 'bg-danger text-white' : ''}}" wire:click="clockout">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <div class="d-flex justify-content-center">
                            <i class="fa-regular fa-circle-xmark"></i>
                        </div>
                        <div class="text-center mt-3">
                            Clock Out
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-3 bg-dark text-white" wire:click="showLogs">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <div class="d-flex justify-content-center">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <div class="text-center mt-3">
                            Clock Logs
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="logs_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Clock Logs</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($logs))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Hours Consumed</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $key => $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}</td>
                                            <td>{{ $item->clock_in ? \Carbon\Carbon::parse($item->clock_in)->format('h:i A') : 'In Progress...' }}</td>
                                            <td>{{ $item->clock_out ? \Carbon\Carbon::parse($item->clock_out)->format('h:i A') : 'In Progress...' }}</td>
                                            <td>
                                                @if ($item->clock_in && $item->clock_out)
                                                    @php
                                                        $clockIn = \Carbon\Carbon::parse($item->clock_in);
                                                        $clockOut = \Carbon\Carbon::parse($item->clock_out);
                                                        $hoursConsumed = $clockIn->diffInHours($clockOut);
                                                        $minutesConsumed = $clockIn->diffInMinutes($clockOut) % 60;
                                                    @endphp
                                                    {{ $hoursConsumed }}h {{ $minutesConsumed }}m
                                                @else
                                                    In Progress...
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->clock_in && $item->clock_out)
                                                    <span class="badge bg-success p-3 text-uppercase fw-bold w-100" style="font-size: 11px">Completed</span>
                                                @elseif ($item->clock_in)
                                                    <span class="badge bg-warning p-3 text-uppercase fw-bold w-100" style="font-size: 11px">In Progress</span>
                                                @else
                                                    <span class="badge bg-secondary p-3 text-uppercase fw-bold w-100" style="font-size: 11px">Not Started</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center fw-bold py-4">No Clock Logs</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">Currently no clock logs.</div>
                    @endif
                </div>                
            </div>
        </div>
    </div>

</div>
