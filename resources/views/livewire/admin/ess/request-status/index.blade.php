<div>
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
        <table class="table table-striped table-bordered w-100" wire:poll.visible>
            <thead>
                <tr>
                    <th></th>
                    <th>Employee No.</th>
                    <th>Employee Name</th>
                    <th>Last Activty</th>
                    <th></th>
                </tr>
            </thead>                
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td class="text-center">
                            <img style="width: 50px; height: 50px;"
                                src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($record->personal->firstname . ' ' . $record->personal->lastname) }}">              
                        </td>
                        <td>{{$record->employee_no}}</td>
                        <td>{{$record->personal->firstname . ' ' . $record->personal->lastname}}</td>
                        <td>
                            @if ($record->latest_message_date)
                                {{ relative_time($record->latest_message_date) }}
                            @else
                                No messages
                            @endif
                        </td>
                        <td>
                            <a href="{{route('ess.messages', ['employee_no' => $record->employee_no])}}" class="btn btn-primary mx-1">
                                <i class="fa-solid fa-paper-plane"></i>
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