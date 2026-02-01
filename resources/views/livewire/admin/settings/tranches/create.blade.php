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
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control">
                            <div class="error-field">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                        <label class="mb-2" for="eligible">
                            Eligible <span class="text-danger">*</span>
                        </label>

                        <select wire:model="eligible" id="eligible" class="form-select">
                            <option value=""> - CHOOSE - </option>

                            @foreach ($employmentTypes as $type)
                                <option value="{{ $type->id }}">
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="error-field">
                            @error('eligible')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                        {{-- Tranche Year --}}
            <div class="col-12 col-md-6 mb-4">
                <label class="mb-2" for="year">
                    Tranche Year <span class="text-danger">*</span>
                </label>

                <select wire:model="year" id="year" class="form-select">
                    <option value=""> - CHOOSE YEAR - </option>

                    @foreach ($years as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>

                <div class="error-field">
                    @error('year')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>


                        {{-- Active --}}
                        <div class="col-12 col-md-6 mb-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="name">File <span class="text-danger">*</span></label>
                            <input type="file" wire:model="file" id="file" class="form-control">
                            <div class="mt-2">
                                <small class="text-muted text-uppercase">(only accepts csv file)</small>
                            </div>
                            <div class="error-field">
                                @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                @error('records') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if($records)
                            <div class="col-12 mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Salary Grade</th>
                                            <th>Step 1</th>
                                            <th>Step 2</th>
                                            <th>Step 3</th>
                                            <th>Step 4</th>
                                            <th>Step 5</th>
                                            <th>Step 6</th>
                                            <th>Step 7</th>
                                            <th>Step 8</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $key => $data)
                                            <tr>
                                                <td>{{ $data['salary_grade'] }}</td>
                                                <td>
                                                     <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_1"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_1_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_2"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_2_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                   <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_3"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_3_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_4"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_4_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_5"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_5_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_6"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_6_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_7"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_7_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="border rounded p-2">
                                                        <label class="form-label mb-1 small text-muted">Salary</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_8"
                                                            class="form-control form-control-sm mb-2">

                                                        <label class="form-label mb-1 small text-muted">WTAX</label>
                                                        <input type="text"
                                                            wire:model.lazy="records.{{ $key }}.step_8_wtax"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif                    
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
