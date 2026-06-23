<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="row mb-3">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item d-flex gap-3 my-3" role="presentation">
                    <a href="{{ route('reports.dtr') }}"
                    class="nav-link text-uppercase fw-bold {{ is_null($selectedType) ? 'active' : '' }}">
                        All
                    </a>

                    @foreach($employmentTypes as $employmentType)
                        <a href="{{ route('reports.dtr', ['type' => $employmentType->id]) }}"
                        class="nav-link text-uppercase fw-bold {{ $selectedType == $employmentType->id ? 'active' : '' }}">
                            {{ $employmentType->name }}
                        </a>
                    @endforeach

                    <a href="{{ route('reports.dtr', ['type' => 'unassigned']) }}"
                    class="nav-link text-uppercase fw-bold {{ $selectedType === 'unassigned' ? 'active' : '' }}">
                        Unassigned
                    </a>
                </li>
            </ul>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex align-items-center gap-2">
            
                        <input
                            type="month"
                            wire:model="selectedMonth"
                            class="form-control"
                            style="max-width: 200px;">
            
                            <button
                            wire:click="downloadAllDtr"
                            wire:loading.attr="disabled"
                            {{ count($selectedEmployees) == 0 ? 'disabled' : '' }}
                            class="btn btn-success">
            
                            <span wire:loading.remove wire:target="downloadAllDtr">
                                <i class="fa fa-download"></i>
                                Download All DTR
                                ({{ count($selectedEmployees) }})
                            </span>
            
                            <span wire:loading wire:target="downloadAllDtr">
                                <span class="spinner-border spinner-border-sm"></span>
                                Generating...
                            </span>
            
                        </button>
            
                    </div>
                </div>
            </div>
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
                            <th width="50">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectAll">
                            </th>
                            <th>Employee No.</th>
                            <th>Name</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedEmployees"
                                        value="{{ $record->employee_no }}">
                                </td>
                                <td>{{$record->employee_no}}</td>
                                <td>
                                    {{ $record->personal->firstname }}
                                    {{ $record->personal->middlename ? substr($record->personal->middlename, 0, 1) . '.' : '' }}
                                    {{ $record->personal->lastname }}
                                </td>                        
                                <td> 
                                    <a target="_blank" href="{{ route('dtr.show', ['id' => $record->employee_no]) }}" class="btn btn-primary">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
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
    {{-- Full Screen Overlay --}}
<div
wire:loading.flex
wire:target="downloadAllDtr"
class="position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center"
style="
    background: rgba(0,0,0,.75);
    z-index: 999999;
    backdrop-filter: blur(4px);
"
>

<div
    class="bg-white rounded shadow-lg p-5 text-center"
    style="min-width: 500px; max-width: 600px;"
>

    <div class="mb-4">
        <div
            class="spinner-border text-success"
            style="width: 5rem; height: 5rem;"
            role="status">
        </div>
    </div>

    <h3 class="fw-bold text-success mb-3">
        Generating DTR ZIP
    </h3>

    <div class="progress mb-3" style="height: 25px;">
        <div
            class="progress-bar progress-bar-striped progress-bar-animated bg-success"
            style="width: 100%;">
            Processing...
        </div>
    </div>

    <p class="mb-2">
        Please wait while employee DTR PDFs are being generated.
    </p>

    <p class="text-muted mb-0">
        This may take several minutes depending on the number of employees.
    </p>

    <div class="alert alert-warning mt-4 mb-0">
        <strong>Do not:</strong>
        refresh, close this tab, logout, or navigate to another page
        until the download starts.
    </div>

</div>

</div>
</div>