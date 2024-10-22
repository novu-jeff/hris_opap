<div>
    <div class="modal fade" wire:ignore.self id="update-profile-skills-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-profile-skills-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="update-profile-skills-modalLabel">Update Skills</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" wire:ignore>
                    <div class="row">
                        <div class="col-12 col-md-12 mb-3">
                            <label for="skills" class="mb-2">skills <span class="text-danger">*</span></label>
                            <select wire:model="skills" id="select" class="select-2" multiple >
                                @foreach ($record->groupBy('category') as $category => $skills)
                                    <optgroup label="{{ $category }}">
                                        @foreach ($skills as $skill)
                                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click='save'>Proceed</button>
                </div>
            </div>
        </div>
    </div> 
</div>

@section('script')
    <script>
        $(function() {
            $('.select-2').selectize({
                onChange: function (value) {
                    Livewire.dispatch('populateField', [value]);
                }
            });
        });
    </script>
@endsection