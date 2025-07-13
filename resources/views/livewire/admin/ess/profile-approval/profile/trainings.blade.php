<div>
    @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                     <tr>
                        <th rowspan="2" class="text-uppercase text-center">Title of learning and development <br> interventions / training programs <br> (Write in full)</th>
                        <th colspan="2" class="text-uppercase text-center">Inclusive Dates of Attendances</th>
                        <th rowspan="2" class="text-uppercase text-center">Number of Hours</th>
                        <th rowspan="2" class="text-uppercase text-center">Type of LD (Managerial / Supervisory / <br> Technician / etc )</th>
                        <th rowspan="2" class="text-uppercase text-center">Conducted / Sponsored By <br> (Write in full)</th>
                        <th rowspan="2" class="text-uppercase text-center">Documents</th>
                    </tr>
                    <tr>
                        <th class="text-uppercase text-center">From</th>
                        <th class="text-uppercase text-center">To</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>
                                <input style="width: 600px" type="text" wire:model="records.{{$key}}.name.new" id="records.{{$key}}.name.new" class="form-control restricted text-center {{$records[$key]['name']['new'] !== $records[$key]['name']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.name.new') <span class="text-danger">{{ $message }}</span> @enderror
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
                                <input style="width: 600px" type="text" wire:model="records.{{$key}}.type.new" id="records.{{$key}}.type.new" class="form-control restricted text-center {{$records[$key]['type']['new'] !== $records[$key]['type']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.type.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>                          
                            <td>
                                <input style="width: 800px" type="text" wire:model="records.{{$key}}.sponsored_by.new" id="records.{{$key}}.sponsored_by.new" class="form-control restricted text-center {{$records[$key]['sponsored_by']['new'] !== $records[$key]['sponsored_by']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.sponsored_by.new') <span class="text-danger">{{ $message }}</span> @enderror
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