<div>
    <div wire:ignore.self class="row d-flex justify-content-center mt-5">
        <div class="col-7">
            <form wire:submit.prevent='save'>
                <div class="card shadow">
                    <div class="card-header bg-transparent border-0 pt-5 px-4 pb-0 mb-0">
                        <h1 class="text-uppercase fw-bold">{{$record->interview->name}}</h1>
                    </div>
                    <div class="card-body pt-0 p-4">
                        <div class="row mt-5">
                            @foreach($record->interview->items as $key => $item)
                                <div class="col-12 mb-4 text-uppercase">
                                    <h6 class="mb-3">{{ $item->name }}</h6>
                                    <input type="text" wire:model="answer.{{ $key }}" id="name" class="form-control text-uppercase" placeholder="Your Answer...">
                                    <div class="error-field">
                                        @error('answer.' . $key) 
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                    </div>
                    <div class="card-footer d-flex justify-content-end py-3">
                        <button type="submit" class="btn btn-primary text-uppercase">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
