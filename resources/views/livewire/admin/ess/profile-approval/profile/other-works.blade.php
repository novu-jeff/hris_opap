<div>
    @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-uppercase text-center">Name & Address of Organization <br> (Write in full)</th>
                        <th colspan="2" class="text-uppercase text-center">Inclusive Dates <br> (mm/dd/yyyy)</th>
                        <th rowspan="2" class="text-uppercase text-center">Number of Hours</th>
                        <th rowspan="2" class="text-uppercase text-center">Position / Nature of Work</th>
                        <th rowspan="2" class="text-uppercase text-center">Documents</th>
                    </tr>
                    <tr>
                        <th class="text-center">From</th>
                        <th class="text-center">To</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>
                                <input style="width: 600px" type="text" wire:model="records.{{$key}}.organization.new" id="records.{{$key}}.organization.new" class="form-control restricted text-center {{$records[$key]['organization']['new'] !== $records[$key]['organization']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.organization.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.date_from.new" id="records.{{$key}}.date_from.new" class="form-control restricted text-center {{$records[$key]['date_from']['new'] !== $records[$key]['date_from']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.date_from.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.date_to.new" id="records.{{$key}}.date_to.new" class="form-control restricted text-center {{$records[$key]['date_to']['new'] !== $records[$key]['date_to']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.date_to.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>  
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.consumed_hours.new" id="records.{{$key}}.consumed_hours.new" class="form-control restricted text-center {{$records[$key]['consumed_hours']['new'] !== $records[$key]['consumed_hours']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.consumed_hours.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>                                         
                            <td>
                                <input style="width: 600px" type="text" wire:model="records.{{$key}}.position.new" id="records.{{$key}}.position.new" class="form-control restricted text-center {{$records[$key]['position']['new'] !== $records[$key]['position']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.position.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>    
                            <td>
                                @if($records[$key]['documents']['old'] || $records[$key]['documents']['new'])
                                    <a href="javascript:void(0)" wire:click.prevent="download('{{$key}}')" class="btn {{$records[$key]['documents']['new'] !== $records[$key]['documents']['old'] ? 'btn-danger' : 'btn-primary'}}">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                @else
                                    <div class="text-muted">N/A</div>
                                @endif
                            </td>                                     
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-danger text-uppercase fw-medium text-center">No data found.</div>
    @endif                                     
</div>