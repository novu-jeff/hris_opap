<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="tab-content mt-5" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
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
                                    <th>Employee No.</th>
                                    <th>Unit</th>
                                    <th>Employee Name</th>
                                    <th>Updates Made</th>
                                    <th>Date Applied</th>
                                    <th style="max-width: 200px;">Action</th>
                                </tr>
                            </thead>                
                            <tbody>
                                @forelse($records as $record)
                                    <tr data-id="{{$record['id']}}">
                                        <td>{{$record['employee_no']}}</td>
                                        <td>
                                            <span class="{{ $record['section_code'] == 'NO SECTION' ? 'text-muted fst-italic' : '' }}">
                                                {{ strtoupper($record['section_code']) }}
                                            </span>
                                        </td>
                                        <td>{{$record['name']}}</td>
                                        <td>{{$record['types']}}</td>
                                        <td>{{$record['date_applied']}}</td>
                                        <td>
                                            <a target="_blank" href="{{route('ess.approval-profile.show', ['employee_no' => $record['employee_no'], 'form' => $record['type']])}}" class="btn btn-primary mx-1">
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
        </div>
    </div>  
</div>
