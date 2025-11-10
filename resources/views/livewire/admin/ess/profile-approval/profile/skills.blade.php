<div>
    @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3 w-100">
                <thead>
                    <tr>
                        <th class="text-center text-uppercase">Special Skills and Hobbies</th>
                        <th class="text-center text-uppercase">Non-Academic Distinctions <br> / Recognition (Write in full)</th>
                        <th class="text-center text-uppercase">Membership in Association / Organization</th>
                        <th class="text-center text-uppercase">Documents</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>
                                <input style="width: 500px" type="text" wire:model="records.{{$key}}.name.new" id="records.{{$key}}.name.new" class="form-control restricted text-center {{$records[$key]['name']['new'] !== $records[$key]['name']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.name.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 600px" type="text" wire:model="records.{{$key}}.recognition.new" id="records.{{$key}}.recognition.new" class="form-control restricted text-center {{$records[$key]['recognition']['new'] !== $records[$key]['recognition']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.recognition.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 800px" type="text" wire:model="records.{{$key}}.organization.new" id="records.{{$key}}.organization.new" class="form-control restricted text-center {{$records[$key]['organization']['new'] !== $records[$key]['organization']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.organization.new') <span class="text-danger">{{ $message }}</span> @enderror
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