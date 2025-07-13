<div>
     @if (!empty($records))
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">Inclusive Dates <br> (mm/dd/yyyy)</th>
                        <th rowspan="2" class="text-center">Position Title <br> (Write in full / Do not abbreviate)</th>
                        <th rowspan="2" class="text-center">Department / Agency / Office / Company <br> (Write in full / Do not abbreviate)</th>
                        <th rowspan="2" class="text-center">Monthly Salary</th>
                        <th rowspan="2" class="text-center">Salary / Job / Pay Grade (if applicable) <br> & Step (Format "00-0") / Increment</th>
                        <th rowspan="2" class="text-center">Status of Appointment</th>
                        <th rowspan="2" class="text-center">Gov't Service (Y / N)</th>
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
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.from_year.new" id="records.{{$key}}.from_year.new" class="form-control restricted text-center {{$records[$key]['from_year']['new'] !== $records[$key]['from_year']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.from_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.to_year.new" id="records.{{$key}}.to_year.new" class="form-control restricted text-center {{$records[$key]['to_year']['new'] !== $records[$key]['to_year']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.to_year.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>    
                            <td>
                                <input style="width: 600px" type="text" wire:model="records.{{$key}}.position.new" id="records.{{$key}}.position.new" class="form-control restricted text-center {{$records[$key]['position']['new'] !== $records[$key]['position']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.position.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 800px" type="text" wire:model="records.{{$key}}.department.new" id="records.{{$key}}.department.new" class="form-control restricted text-center {{$records[$key]['department']['new'] !== $records[$key]['department']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.department.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.monthly_salary.new" id="records.{{$key}}.monthly_salary.new" class="form-control restricted text-center {{$records[$key]['monthly_salary']['new'] !== $records[$key]['monthly_salary']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.monthly_salary.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input style="width: 300px" type="text" wire:model="records.{{$key}}.salary_pay_grade.new" id="records.{{$key}}.salary_pay_grade.new" class="form-control restricted text-center {{$records[$key]['salary_pay_grade']['new'] !== $records[$key]['salary_pay_grade']['old'] ? 'border-danger border-3' : ''}}">
                                <div class="error-field">
                                    @error('records.'.$key.'.salary_pay_grade.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <select disabled style="width: 300px" wire:model="records.{{$key}}.employment_status.new" id="records.{{$key}}.employment_status.new" class="form-select restricted {{$records[$key]['employment_status']['new'] !== $records[$key]['employment_status']['old'] ? 'border-danger border-3' : ''}}">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="regular">Regular</option>
                                    <option value="part time">Part Time</option>
                                    <option value="freelance">Freelance</option>
                                    <option value="project base">Project Base</option>
                                </select>
                                <div class="error-field">
                                    @error('records.'.$key.'.employment_status.new') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <select disabled style="width: 300px" wire:model="records.{{$key}}.isGovernment.new" id="records.{{$key}}.isGovernment.new" class="form-select restricted {{$records[$key]['isGovernment']['new'] !== $records[$key]['isGovernment']['old'] ? 'border-danger border-3' : ''}}">
                                    <option value=""> - CHOOSE - </option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                                <div class="error-field">
                                    @error('records.'.$key.'.isGovernment.new') <span class="text-danger">{{ $message }}</span> @enderror
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