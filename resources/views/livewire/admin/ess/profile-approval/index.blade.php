<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0" wire:ignore>
            <div class="tab-content mt-5" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <table class="table w-100 data-tables" wire:ignore>
                        <thead>
                            <tr>
                                <th>Employee No.</th>
                                <th>Employee Name</th>
                                <th>Date Applied</th>
                                <th style="max-width: 200px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($records as $record)
                                <tr data-id="{{$record->id}}">
                                    <td>{{$record->employee_no}}</td>
                                    <td>{{$record->firstname . ' ' . $record->lastname}}</td>
                                    <td>{{format_date($record->created_at, 'date_string')}}</td>
                                    <td>
                                        <a target="_blank" href="{{route('ess.approval-profile.edit', ['approval' => $record->employee_no])}}" class="btn btn-primary mx-1">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
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
    </div>  
</div>
