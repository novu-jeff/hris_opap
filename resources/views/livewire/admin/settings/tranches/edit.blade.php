<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                {{-- Header --}}
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">
                        All <span class="text-danger">*</span> is required
                    </p>
                </div>
                <hr class="mx-3">

                {{-- Body --}}
                <div class="card-body">
                    <div class="row">
                        {{-- Name --}}
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control">
                            <div class="error-field">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Eligible --}}
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="eligible">Eligible <span class="text-danger">*</span></label>
                            <select wire:model="eligible" id="eligible" class="form-select">
                            <option value=""> - CHOOSE - </option>

                                @foreach ($employmentTypes as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('eligible') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                      {{-- Tranche Year --}}
                    <div class="col-12 col-md-6 mb-4">
                    <label class="mb-2">Tranche Year <span class="text-danger">*</span></label>

                    {{-- Read-only display --}}
                    <div class="form-control bg-light">
                        {{ $year }}
                    </div>

                    {{-- Hidden field so Livewire keeps the value --}}
                    <input type="hidden" wire:model="year">

                    <div class="error-field">
                        @error('year') <span class="text-danger">{{ $message }}</span> @enderror
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

                        {{-- File Upload --}}
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="file">File </label>
                            <input type="file" wire:model="file" id="file" class="form-control">
                            <div class="mt-2">
                                <small class="text-muted text-uppercase">(only accepts csv file)</small>
                            </div>
                            <div class="error-field">
                                @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                @error('records') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Salary Steps Table --}}
                        @if($records)
                            <div class="col-12 mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Salary Grade</th>
                                                @foreach(range(1,8) as $i)
                                                    <th>Step {{ $i }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $key => $data)
                                                <tr>
                                                    <td>{{ $data['salary_grade'] }}</td>
                                                    @foreach(range(1,8) as $i)
                                                        <td>
                                                            <div class="border rounded p-2">
                                                                <label class="form-label mb-1 small text-muted">Salary</label>
                                                                <input type="text" wire:model="records.{{ $key }}.step_{{ $i }}"
                                                                    class="form-control form-control-sm mb-2">

                                                                <label class="form-label mb-1 small text-muted">WTAX</label>
                                                                <input type="text" wire:model="records.{{ $key }}.step_{{ $i }}_wtax"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <hr class="mx-3">

                {{-- Footer --}}
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">
                            Save <i class="fa-solid fa-arrow-right ms-2"></i>
                        </span>
                        <span wire:loading wire:target="save">
                            Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
