<div>
    @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center">Career Service/ RA 1080 (Board / Bar) Under Special Laws/ CES/ CSEE<br>Barangay Eligibility / Driver's License</th>
                        <th rowspan="2" class="text-center">Rating <br> (if applicable)</th>
                        <th rowspan="2" class="text-center">Date of Examination / Conferment</th>
                        <th rowspan="2" class="text-center">Place of Exam / Conferment</th>
                        <th colspan="2" class="text-center">License (if applicable)</th>
                        <th rowspan="2" class="text-center">Documents</th>
                    </tr>
                    <tr>
                        <th class="text-center">Number</th>
                        <th class="text-center">Date of validity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>
                                <input style="width: 800px" type="text" wire:model="records.{{$key}}.certification.new" id="records.{{$key}}.certification.new" class="form-control restricted text-center {{$records[$key]['certification']['new'] !== $records[$key]['certification']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.certification.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.rating.new" id="records.{{$key}}.rating.new" class="form-control restricted text-center {{$records[$key]['rating']['new'] !== $records[$key]['rating']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.rating.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.date_exam.new" id="records.{{$key}}.date_exam.new" class="form-control restricted text-center {{$records[$key]['date_exam']['new'] !== $records[$key]['date_exam']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.date_exam.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.place_exam.new" id="records.{{$key}}.place_exam.new" class="form-control restricted text-center {{$records[$key]['place_exam']['new'] !== $records[$key]['place_exam']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.place_exam.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>                                         
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.license_no.new" id="records.{{$key}}.license_no.new" class="form-control restricted text-center {{$records[$key]['license_no']['new'] !== $records[$key]['license_no']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.license_no.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.date_validity.new" id="records.{{$key}}.date_validity.new" class="form-control restricted text-center {{$records[$key]['date_validity']['new'] !== $records[$key]['date_validity']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.date_validity.new') <span class="text-danger">{{ $message }}</span> @enderror
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