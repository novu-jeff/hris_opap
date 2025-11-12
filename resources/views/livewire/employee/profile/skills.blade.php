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
                <table class="table table-bordered mt-3 w-100">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase"></th>
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
                                    <button type="button" class="btn btn-danger" wire:click="removeRecord('true', {{$key}})">
                                        <i class="fa-solid fa-circle-minus"></i>
                                    </button>
                                </td>
                                <td>
                                    <input style="width: 500px" type="text" wire:model="records.{{$key}}.name" id="records.{{$key}}.name" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        @error('records.'.$key.'.name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 600px" type="text" wire:model="records.{{$key}}.recognition" id="records.{{$key}}.recognition" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        @error('records.'.$key.'.recognition') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 800px" type="text" wire:model="records.{{$key}}.organization" id="records.{{$key}}.organization" class="form-control text-uppercase text-center">
                                    <div class="error-field">
                                        @error('records.'.$key.'.organization') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </td>    
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <input type="file" style="width: 300px;" wire:model="records.{{$key}}.documents" id="records.{{$key}}.documents" class="form-control">
                                        </div>
                                        @if($records[$key]['documents'])
                                            <div>
                                                <a href="javascript:void(0)" wire:click.prevent="download('{{$key}}')" class="btn btn-primary">
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
            <div class="alert alert-danger text-uppercase fw-medium text-center">No data found.</div>
        @endif            
        <div class="card-footer mt-5 pb-3 d-flex justify-content-end bg-transparent border-0">
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                    <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                    <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>
</form>

