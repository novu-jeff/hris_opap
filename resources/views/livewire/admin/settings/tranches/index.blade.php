<div>
    <div>
    <div class="modal fade"
     id="show"
     wire:ignore.self
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     tabindex="-1">

     <div class="modal-dialog custom-modal modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 rounded-3">

            {{-- HEADER --}}
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-semibold text-dark">
                    Salary Table <span class="text-muted">— {{ $show->name ?? '' }}</span>
                </h5>
                
                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="toggleView()"
                            id="viewToggle">
                        Card View
                    </button>
                
                   
             

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body px-4 py-3">

                @if($show)
                <div id="tableView">
                    <div class="table-responsive">

                        <table class="table align-middle mb-0 salary-table sticky-first">

                            {{-- HEADER --}}
                            <thead class="text-center">
                                <tr class="border-bottom">
                                    <th rowspan="2" class="bg-light fw-semibold">SG</th>
                                    <th colspan="8" class="bg-light fw-semibold">Steps</th>
                                </tr>
                                <tr class="bg-light small text-muted">
                                    @for($i=1;$i<=8;$i++)
                                        <th>{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>

                            {{-- BODY --}}
                            <tbody>
                                @foreach($show->items as $data)

                                    <tr class="salary-row">
                                        <td class="text-center fw-bold text-primary">
                                            {{ $data['salary_grade'] }}
                                        </td>

                                        @for($i=1;$i<=8;$i++)
                                            <td class="text-end">
                                                ₱{{ number_format((float)$data['step_'.$i], 2) }}
                                            </td>
                                        @endfor
                                    </tr>

                                    {{-- WTAX --}}
                                    @if($showWtax)
                                        <tr class="wtax-row">
                                            <td></td>
                                            @for($i=1;$i<=8;$i++)
                                                <td class="text-end text-muted small">
                                                    {{ number_format($data['step_'.$i.'_wtax'], 2) }}
                                                </td>
                                            @endfor
                                        </tr>
                                    @endif

                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>  

                <div id="cardView" class="d-none">
                    @foreach($show->items as $data)
                        <div class="salary-card mb-3">
                            <div class="fw-bold text-primary mb-2">
                                SG {{ $data['salary_grade'] }}
                            </div>
                
                            <div class="row g-2">
                                @for($i=1;$i<=8;$i++)
                                    <div class="col-6">
                                        <div class="card-step">
                                            <div class="label">Step {{ $i }}</div>
                                            <div class="value">
                                                ₱{{ number_format((float)$data['step_'.$i], 2) }}
                                            </div>
                
                                            @if($showWtax)
                                                <div class="wtax">
                                                    {{ number_format($data['step_'.$i.'_wtax'], 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
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
                                <td>{{ $tranche->employmentType->name ?? 'N/A' }}</td>
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

@section('script')
<script>
    function toggleView() {
        const table = document.getElementById('tableView');
        const card = document.getElementById('cardView');
        const btn = document.getElementById('viewToggle');
    
        if (table.classList.contains('d-none')) {
            table.classList.remove('d-none');
            card.classList.add('d-none');
            btn.innerText = 'Card View';
        } else {
            table.classList.add('d-none');
            card.classList.remove('d-none');
            btn.innerText = 'Table View';
        }
    }
    </script>   
@endsection