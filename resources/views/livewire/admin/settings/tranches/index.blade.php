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


    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="row mb-4">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label for="entries" class="form-label mb-0">Show entries:</label>
                    <select id="entries" wire:model.live="entries" class="form-select w-auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="60">60</option>
                        <option value="70">70</option>
                        <option value="80">80</option>
                        <option value="90">90</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                    <label for="search" class="form-label mb-0">Search:</label>
                    <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @forelse($records as $record)
                            <tr data-id="{{$record->id}}">
                                <td>{{$record->name}}</td>
                                <td>
                                    <button wire:click="view('{{$record->id}}')" class="btn btn-primary mx-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="{{route('tranches.edit', ['tranch' => $record->id])}}" class="btn btn-primary mx-1">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $records->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>    
</div>