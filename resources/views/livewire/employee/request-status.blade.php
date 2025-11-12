<div>
    <div class="row" wire:ignore.self>
        <div class="col-12" wire:click="makeSeen">
            <div class="chat-area" >
                <div class="chatbox">
                    <div class="modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="msg-head">
                                <div class="row ">
                                    <div class="col-12 col-lg-8">
                                        <div class="px-lg-4 d-flex align-items-center">
                                            <span class="chat-icon"><img class="img-fluid" src="{{asset('img/logo.png')}}" alt="image title"></span>
                                            <div class="flex-shrink-0">
                                                <img class="img-fluid" src="{{asset('img/logo.png')}}" alt="user img" style="width: 80px;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h3>Human Resources (HR)</h3>
                                                <p>Administrator</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="msg-body" wire:poll="loadRecords" wire:poll.keep-alive>
                                    <ul>
                                        @foreach ($records as $message)
                                            @if (!empty($message['message']) || !$message['attachments']->isEmpty())
                                                <li class="{{ $message['from_role'] === 'admin' ? 'sender' : 'reply' }} {{ !$message['attachments']->isEmpty() ? 'active' : '' }}">
                                                    @if (!empty($message['message']))
                                                        <p>{{ $message['message'] }}</p>
                                                        @if($message['from_role'] == 'admin')
                                                            <span class="time">{{ relative_time($message['created_at']) }}</span>
                                                        @else
                                                            @if($message['isSeen'])
                                                                <span class="time">Seen at {{ format_date($message['created_at'], 'day_date_time_string') }}</span>
                                                            @else
                                                                <span class="time">{{ relative_time($message['created_at']) }}</span>
                                                            @endif

                                                        @endif
                                                    @endif
                                                    
                                                    @if (!empty($message['attachments']))
                                                        <div class="attachments mt-2">
                                                            @foreach ($message['attachments'] as $attachment)
                                                                @php
                                                                    $filePath = Storage::url('public/messages/' . $attachment['attachment']);
                                                                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                                @endphp
                                            
                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <img src="{{ $filePath }}" alt="Image Attachment" class="img-fluid mt-2" style="max-width: 200px; height: auto; object-fit: cover;">
                                                                
                                                                @elseif (in_array($extension, ['doc', 'docs', 'docx', 'xls', 'xlsx', 'pdf']))
                                                                    <p>
                                                                        <a wire:click="download({{ $message['id'] }}, {{ $attachment->id }})" href="javascript:void(0)" class="nav-link d-flex align-items-center gap-2">
                                                                            <i class="fa-solid fa-download"></i>
                                                                            {{$attachment['original']}}
                                                                        </a>
                                                                    </p>
                                                                @endif
                                                                @if (empty($message['message']))
                                                                    @if($message['from_role'] != 'admin')
                                                                        <span class="time">{{ relative_time($message['created_at']) }}</span>
                                                                    @else

                                                                        @if($message['isSeen'])
                                                                            <span class="time">Seen at {{ format_date($message['created_at'], 'day_date_time_string') }}</span>
                                                                        @else
                                                                            <span class="time">{{ relative_time($message['created_at']) }}</span>
                                                                        @endif

                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="send-box w-100" wire:click="makeSeen">
                                <form wire:submit.prevent="send" wire:target="send" class="mb-0 ">
                                    <div class="d-lg-flex gap-3">
                                        <div class="form-group w-100">
                                            <label for="message" class="visually-hidden">Message</label>
                                            <textarea
                                                wire:model.defer="message"
                                                id="message"
                                                cols="30"
                                                rows="1"
                                                class="w-100 form-control @error('message') invalid-feed @enderror"
                                                placeholder="Type something..."
                                            ></textarea>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary send w-100">
                                                <span wire:loading.remove wire:target="send">
                                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                                    Send
                                                </span>
                                                <span wire:loading wire:target="send">
                                                    <i class="fa-solid fa-spinner ms-2 fa-spin"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>                
                                <div class="error-field mt-2">
                                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                                    @error('attachments') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="send-btns mt-4">
                                    <div class="attach">
                                        <div class="button-wrapper btn btn-primary text-white px-4 py-2 rounded-2">
                                            <span class="label text-uppercase fw-bold text-white" style="cursor: pointer">Upload attachments</span>
                                            <input type="file" wire:model.live="attachments" multiple id="upload" class="upload-box" placeholder="Upload File" aria-label="Upload File">
                                        </div>
                                        <div>
                                            <small class="text-muted fw-bold text-uppercase">Note: Maximum of 5 files allowed, with each file no larger than 5 MB.</small>
                                        </div>
                                        @if (isset($preview_attachments))
                                            {{-- Image Grid --}}
                                            <div class="attachment-grid mt-4">
                                                @foreach($preview_attachments as $index => $attachment)
                                                    @if ($attachment['type'] === 'image')
                                                        <div class="attachment-item">
                                                            <img src="{{ $attachment['url'] }}" class="img-fluid" alt="Preview Image" style="height: 100%; width: 100%; object-fit: scale-down;">
                                                            <button type="button" class="btn btn-sm btn-danger" wire:click="remove({{$index}})">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        
                                            {{-- File List --}}
                                            <div class="attachment-files">
                                                @foreach($preview_attachments as $index => $attachment)
                                                    @if ($attachment['type'] === 'file')
                                                        <div class="mb-2 d-flex align-items-center gap-2">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <i class="{{ format_extension($attachment['url']) }}"></i> 
                                                                {{ format_getFileName($attachment['url']) }}
                                                                <div>
                                                                    <button type="button" class="btn btn-sm btn-danger" wire:click="remove({{$index}})">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
