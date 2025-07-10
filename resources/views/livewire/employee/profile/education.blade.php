<form wire:submit.prevent="save" wire:target="save">
    <div>
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-info px-3 py-2 text-uppercase fw-bold" wire:click="addRecord">
                <span wire:loading.remove wire:target="addRecord">Add Record </span>
                <span wire:loading wire:target="addRecord"><i class="fa-solid fa-spinner px-2 fa-spin"></i></span>
            </button>
        </div>
        @if (!empty($records))
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Level</th>
                            <th>Name of School</th>
                            <th>Basic Education / Strand / Degree / Course</th>
                            <th>Attended From</th>
                            <th>Attended To</th>
                            <th>Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $key => $item)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-danger" wire:click="removeRecord('{{$key}}')">
                                    <i class="fa-solid fa-circle-minus"></i>
                                </button>
                            </td>
                            <td>
                                <select style="width: 300px" wire:model="records.{{$key}}.level" id="records.{{$key}}.level" class="form-select text-uppercase">
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
                                    @error('records.'.$key.'.level') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="text" style="width: 300px" wire:model="records.{{$key}}.school_name" id="records.{{$key}}.school_name" class="form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.'.$key.'.school_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="text" style="width: 300px" wire:model="records.{{$key}}.course" id="records.{{$key}}.course" class="form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.'.$key.'.course') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="date" style="width: 300px" wire:model="records.{{$key}}.from_year" id="records.{{$key}}.from_year" class="form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.'.$key.'.from_year') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <input type="date" style="width: 300px" wire:model="records.{{$key}}.to_year" id="records.{{$key}}.to_year" class="form-control text-uppercase">
                                <div class="error-field">
                                    @error('records.'.$key.'.to_year') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <input type="file" style="width: 300px;" wire:change="setActiveAccordion('education')" wire:model="records.{{$key}}.documents" id="records.{{$key}}.documents" class="form-control">
                                    </div>
                                    @if($records[$key]['documents'])
                                        <div>
                                            <a href="javascript:void(0)" wire:click.prevent="download('documents', 'children', '{{$key}}')" class="btn btn-primary">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="error-field">
                                    @error('records.'.$key.'.documents') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-uppercase fw-medium text-center">No Education Found.</div>
        @endif            
        <div class="card-footer d-flex justify-content-end bg-transparent border-0 px-5">
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                    <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                    <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                </button>
                @if ($errors->any())
                    <div class="mt-4 pb-5">
                        <small class="text-danger">There's an error upon submitting, please review your form.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>

