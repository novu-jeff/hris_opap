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
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Date of Birth</th>
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
                                    <input type="text" style="width: 300px" wire:model="records.{{$key}}.firstname" id="records.{{$key}}.firstname" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('records.'.$key.'.firstname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>
                                <td>
                                    <input type="text" style="width: 300px" wire:model="records.{{$key}}.middlename" id="records.{{$key}}.middlename" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('records.'.$key.'.middlename') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>
                                <td>
                                    <input type="text" style="width: 300px" wire:model="records.{{$key}}.lastname" id="records.{{$key}}.lastname" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('records.'.$key.'.lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>
                                <td>
                                    <input type="date" style="width: 300px" wire:model="records.{{$key}}.birthdate" id="records.{{$key}}.birthdate" class="form-control text-uppercase">
                                    <div class="error-field">
                                        @error('records.'.$key.'.birthdate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>     
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <input type="file" style="width: 300px;" wire:change="setActiveAccordion('children')" wire:model="records.{{$key}}.documents" id="records.{{$key}}.documents" class="form-control">
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
            <div class="alert alert-info text-uppercase fw-medium text-center">No Children Found.</div>
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

