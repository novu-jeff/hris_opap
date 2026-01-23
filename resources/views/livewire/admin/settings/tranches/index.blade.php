<div>
    <div>
    <div class="modal fade"
         id="show"
         wire:ignore.self
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-uppercase fw-bold">
                        Salary Table – {{ $show->name ?? '' }}
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if($show)
                        {{-- WTAX Toggle --}}
                       <!-- <div class="mb-3">
                            <input type="checkbox" wire:model="showWtax" id="showWtax">
                            <label for="showWtax" class="form-label mb-0">Show WTAX</label>
                        </div>-->

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th rowspan="2">Salary Grade</th>
                                        <th colspan="8">Steps</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($show->items as $data)

                                     @php
                                        $gradeBg = $loop->even ? 'bg-primary bg-opacity-25' : 'bg-primary bg-opacity-10';
                                    @endphp
                                                                        {{-- SALARY ROW --}}
                                        <tr class="fw-semibold">
                                            <td rowspan="{{ $showWtax ? '2' : '1' }}" class="text-center {{ $gradeBg }}">
                                                {{ $data['salary_grade'] }}
                                            </td>

                                            <td>₱{{ number_format((float) $data['step_1'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_2'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_3'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_4'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_5'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_6'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_7'], 2) }}</td>
                                            <td>₱{{ number_format((float) $data['step_8'], 2) }}</td>
                                        </tr>

                                        {{-- WTAX ROW (Conditional) --}}
                                        @if($showWtax)
                                            <tr class="text-muted small fst-italic">
                                                <td>WTAX: ₱{{ number_format($data['step_1_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_2_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_3_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_4_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_5_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_6_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_7_wtax'], 2) }}</td>
                                                <td>WTAX: ₱{{ number_format($data['step_8_wtax'], 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>


   @foreach($tranchesByYear as $year => $yearTranches)
    <div class="card mb-2">
        <div class="card-header bg-primary text-white" data-bs-toggle="collapse" data-bs-target="#year-{{ $year }}">
            Year: {{ $year }}
        </div>
        <div id="year-{{ $year }}" class="collapse">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Name</th>
                            <th>Eligible</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearTranches as $tranche)
                            <tr>
                                <td>{{ $tranche->name }}</td>
                                <td>{{ $tranche->eligible }}</td>
                                <td>{{ $tranche->is_active ? 'Yes' : 'No' }}</td>
                                <td>
                                    <button wire:click="view({{ $tranche->id }})" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="{{ route('tranches.edit', $tranche->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <button wire:click="remove(true, {{ $tranche->id }})" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach

</div>