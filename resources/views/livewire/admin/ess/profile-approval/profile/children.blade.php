<div>
    @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Date of Birth</th>
                        <th>Document</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>
                                <input type="text" readonly wire:model="records.{{$key}}.firstname.new" id="records.{{$key}}.firstname.new" class="form-control text-center restricted {{$records[$key]['firstname']['new'] !== $records[$key]['firstname']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="text" readonly wire:model="records.{{$key}}.middlename.new" id="records.{{$key}}.middlename.new" class="form-control text-center restricted {{$records[$key]['middlename']['new'] !== $records[$key]['middlename']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="text" readonly wire:model="records.{{$key}}.lastname.new" id="records.{{$key}}.lastname.new" class="form-control text-center restricted {{$records[$key]['lastname']['new'] !== $records[$key]['lastname']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="text" wire:model="records.{{$key}}.birthdate.new" id="records.{{$key}}.birthdate.new" class="form-control text-center restricted {{$records[$key]['birthdate']['new'] !== $records[$key]['birthdate']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.birthdate') <span class="text-danger">{{ $message }}</span> @enderror
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