<div>
    <div class="modal fade" id="show" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Salary for {{$show->name ?? ''}}</h1>
                    <button type="button" class="btn-close"  data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($show)
                        <div class="col-12 mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Salary Grade</th>
                                        <th>Step 1</th>
                                        <th>Step 2</th>
                                        <th>Step 3</th>
                                        <th>Step 4</th>
                                        <th>Step 5</th>
                                        <th>Step 6</th>
                                        <th>Step 7</th>
                                        <th>Step 8</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($show->items as $key => $data)
                                        <tr>
                                            <td>{{ $data['salary_grade'] }}</td>
                                            <td>{{ $data['step_1'] }}</td>
                                            <td>{{ $data['step_2'] }}</td>
                                            <td>{{ $data['step_3'] }}</td>
                                            <td>{{ $data['step_4'] }}</td>
                                            <td>{{ $data['step_5'] }}</td>
                                            <td>{{ $data['step_6'] }}</td>
                                            <td>{{ $data['step_7'] }}</td>
                                            <td>{{ $data['step_8'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif 
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