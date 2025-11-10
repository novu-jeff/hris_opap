<div>
    @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center">Level</th>
                        <th rowspan="2" class="text-center">Name of School</th>
                        <th rowspan="2" class="text-center">Basic Education / Degree / Course</th>
                        <th colspan="2" class="text-center">Period of Attendance</th>
                        <th rowspan="2" class="text-center">Highest Level / Units Earned <br> (if not graduated)</th>
                        <th rowspan="2" class="text-center">Year Graduated</th>
                        <th rowspan="2" class="text-center">Scholarship / Academic <br> Honors Received</th>
                        <th rowspan="2" class="text-center">Documents</th>
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
                            <select disabled style="width: 300px" wire:model="records.{{$key}}.level.new" id="records.{{$key}}.level.new" class="form-select restricted {{$records[$key]['level']['new'] !== $records[$key]['level']['old'] ? 'border-danger border-3' : ''}}">
                                <option value=""> - CHOOSE - </option>
                                <option value="elementary">Elementary</option>
                                <option value="secondary">Secondary</option>
                                <option value="vocational">Vocational</option>
                                <option value="highschool">High School</option>
                                <option value="senior_highschool">Senior High School</option>
                                <option value="college">College</option>
                                <option value="masters">Masters</option>
                                <option value="doctoral">Doctoral</option>
                            </select>                                                
                            <div class="error-field">
                                @error('records.'.$key.'.level.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" readonly style="width: 600px" wire:model="records.{{$key}}.school_name.new" id="records.{{$key}}.school_name.new" class="form-control restricted {{$records[$key]['school_name']['new'] !== $records[$key]['school_name']['old'] ? 'border-danger border-3' : ''}}">
                            <div class="error-field">
                                @error('records.'.$key.'.school_name.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" readonly style="width: 800px" wire:model="records.{{$key}}.course.new" id="records.{{$key}}.course.new" class="form-control restricted {{$records[$key]['course']['new'] !== $records[$key]['course']['old'] ? 'border-danger border-3' : ''}}">
                            <div class="error-field">
                                @error('records.'.$key.'.course.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" style="width: 300px" wire:model="records.{{$key}}.from_year.new" id="records.{{$key}}.from_year.new" class="form-control restricted {{$records[$key]['from_year']['new'] !== $records[$key]['from_year']['old'] ? 'border-danger border-3' : ''}}">
                            <div class="error-field">
                                @error('records.'.$key.'.from_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" style="width: 300px" wire:model="records.{{$key}}.to_year.new" id="records.{{$key}}.to_year.new" class="form-control restricted {{$records[$key]['to_year']['new'] !== $records[$key]['to_year']['old'] ? 'border-danger border-3' : ''}}">
                            <div class="error-field">
                                @error('records.'.$key.'.to_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" style="width: 300px" wire:model="records.{{$key}}.highest_level.new" id="records.{{$key}}.highest_level.new" class="{{$records[$key]['highest_level']['new'] !== $records[$key]['highest_level']['old'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                            <div class="error-field">
                                @error('records.'.$key.'.highest_level.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="number" style="width: 300px" wire:model="records.{{$key}}.year_graduated.new" id="records.{{$key}}.year_graduated.new" class="{{$records[$key]['year_graduated']['new'] !== $records[$key]['year_graduated']['old'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                            <div class="error-field">
                                @error('records.'.$key.'.year_graduated.new') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" style="width: 800px" wire:model="records.{{$key}}.scholarship_honors.new" id="records.{{$key}}.scholarship_honors.new" class="{{$records[$key]['scholarship_honors']['new'] !== $records[$key]['scholarship_honors']['old'] ? 'border-danger border-3' : ''}} form-control text-uppercase">
                            <div class="error-field">
                                @error('records.'.$key.'.scholarship_honors.new') <span class="text-danger">{{ $message }}</span> @enderror
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