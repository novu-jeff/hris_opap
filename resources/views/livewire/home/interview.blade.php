<div>
    <div wire:ignore.self class="row d-flex justify-content-center mt-5">
        <div class="col-7">
            @if (!$record->isInterviewResponded)
                <form wire:submit.prevent='save'>
                    <div class="accordion" id="accordionInterview">
                        @foreach ($record->interview as $interviewIndex => $interview)
                            <div class="accordion-item mb-3 shadow-sm border-1">
                                <h2 class="accordion-header" id="headingInterview{{ $interviewIndex }}">
                                    <button class="accordion-button text-uppercase {{ $interviewIndex == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInterview{{ $interviewIndex }}" aria-expanded="{{ $interviewIndex == 0 ? 'true' : 'false' }}" aria-controls="collapseInterview{{ $interviewIndex }}">
                                        Interview #{{ $interviewIndex + 1 }} ({{ $interview->id }})
                                    </button>
                                </h2>
                                <div id="collapseInterview{{ $interviewIndex }}" class="accordion-collapse collapse {{ $interviewIndex == 0 ? 'show' : '' }}" aria-labelledby="headingInterview{{ $interviewIndex }}" data-bs-parent="#accordionInterview">
                                    <div class="accordion-body p-4">
                                        <div class="row ">
                                            @foreach($interview->items as $index => $item)
                                                <div class="col-12 mb-4 text-uppercase">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="count d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; background-color: #225F8B">
                                                            {{$index + 1}}
                                                        </div>
                                                        <div class="question w-100">
                                                            <h6 class="mb-0">{{$item['question']}}</h6>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        @if ($item['response_type'] == 'simple')
                                                            <div class="col-12 mb-3">
                                                                <input type="text" wire:model="answer.{{$item->id}}" class="form-control text-uppercase" placeholder="Your Answer">
                                                            </div>
                                                        @elseif ($item['response_type'] == 'explanatory')
                                                            <div class="col-12 mb-3">
                                                                <textarea wire:model="answer.{{$item->id}}" class="form-control text-uppercase" rows="5" placeholder="Your Answer"></textarea>
                                                            </div>
                                                        @elseif ($item['response_type'] == 'checkbox')
                                                            <div class="col-12 mb-3">
                                                                @foreach ($item['options'] as $optionIndex => $option)
                                                                    <div class="ms-5 form-check d-flex align-items-center gap-3">
                                                                        <input wire:model="answer.{{$item->id}}.{{$option->id}}" type="checkbox" class="form-check-input" id="checkbox-{{ $interviewIndex }}-{{ $index }}-{{ $option->id }}" value="{{ $option->id }}" style="width: 1.5em; height: 1.5em">
                                                                        <label class="form-check-label mt-1 mb-0" for="checkbox-{{ $interviewIndex }}-{{ $index }}-{{ $option->id }}"> 
                                                                            {{ $option->name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif ($item['response_type'] == 'radio')
                                                            <div class="col-12 mb-3">
                                                                @foreach ($item['options'] as $optionIndex => $option)
                                                                    <div class="ms-5 form-check d-flex align-items-center gap-3">
                                                                        <input wire:model="answer.{{$item->id}}" type="radio" class="form-check-input" style="width: 1.5em; height: 1.5em" value="{{ $option->id }}" id="radio-{{ $interviewIndex }}-{{ $index }}-{{ $optionIndex }}">
                                                                        <p class="form-check-label mt-1 mb-0" for="radio-{{ $interviewIndex }}-{{ $index }}-{{ $optionIndex }}">
                                                                            {{ $option->name }}
                                                                        </p>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif ($item['response_type'] == 'file')
                                                            <div class="col-12 mb-3">
                                                                <input type="file" wire:model="answer.{{$item->id}}" name="file_upload" class="form-control">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="error-field ms-5">
                                                        @error('answer.{{$interview->id}}.{{$item->id}}') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary text-uppercase fw-bold px-5 py-3 fs-6">Submit</button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-0 text-uppercase text-center">You've already responded to this interview.</div>
            @endif
        </div>
    </div>

</div>
