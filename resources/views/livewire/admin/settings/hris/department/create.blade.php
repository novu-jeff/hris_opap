<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fields.name" id="fields.name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('fields.name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.cost_center">Cost Center <span class="text-danger">*</span></label>
                            <select wire:model="fields.cost_center" id="fields.cost_center" class="form-select">
                                <option value=""> - Choose - </option>
                                @foreach ($cost_centers as $cost_center)
                                    <option value="{{$cost_center->id}}">{{$cost_center->name}}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('fields.cost_center') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Proceed</button>
                </div>
            </div>
        </div>
    </div>
</form>
