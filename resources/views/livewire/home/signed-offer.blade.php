<div>
    <div wire:ignore.self class="row d-flex justify-content-center mt-5">
        <div class="col-7">
            @if (!$record->isSignedJobOffer)
                <form wire:submit.prevent='save'>
                    <div class="jobs-lists">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card shadow px-2">
                                    <div class="card-header border-0 bg-transparent">
                                        <div class="position-title">
                                            <h4 class="m-0 text-uppercase">{{$record->job->position}}</h4>
                                        </div>
                                        <div class="company-info">
                                            <p class="m-0 text-uppercase">{{$record->job->company_name}}</p>
                                            <p class="m-0 text-uppercase">{{$record->job->location}}</p>
                                        </div>
                                        <div class="date-posted">
                                            <p class="m-0">
                                                Posted {{relative_time($record->job->created_at, 'hours ago')}}
                                            </p>
                                        </div>
                                    </div>
                                    <hr class="mx-3">
                                    <div class="card-body pt-1 pb-4">
                                        <div class="d-flex gap-3 justify-content-start">
                                            <!-- <button type="button" wire:click="go_back" class="btn btn-primary text-uppercase fw-bold px-5 py-3 fs-6">Go Back</button> -->
                                            <a href="{{route('home.view-job', ['slug' => $record->job->slug])}}" class="btn btn-outline-primary d-flex align-items-center gap-2 px-5 py-3 text-uppercase fw-bold">View Job</a>
                                        </div>   
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <label for="file" class="mb-2">Signed Job Offer <span class="text-danger">*</span></label>
                                                <input type="file" wire:model="offer" id="offer" class="form-control">
                                                @if (isset($preview_offer))
                                                    <iframe src="{{ $preview_offer}}" width="100%" height="500px" class="mt-3"></iframe>                                                    
                                                @endif
                                                <div class="error-field mt-3">
                                                    @error('offer') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-5 d-flex justify-content-end">
                                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary text-uppercase fw-bold px-5 py-3 fs-6">
                                                <span wire:loading.remove>Submit</span>    
                                                <span wire:loading>Submitting <i class="fa-solid fa-spinner fa-spin"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-info text-uppercase text-center">Job Offer Already Signed.</div>
                <div class="mt-3 d-flex justify-content-center">
                    <!-- <button type="button" wire:click="go_back" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Go Back</button> -->
                </div>
            @endif
        </div>
    </div>

</div>
