<div>
    <div class="row" wire:ignore.self>
        <div class="col-12" wire:click="makeSeen">
            <div class="chat-area" >
                <div class="chatbox">
                    <div class="modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="msg-head">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="px-4 d-flex align-items-center">
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
                                                                
                                                                @elseif ($extension === 'pdf')
                                                                    <p>
                                                                        <a wire:click="download({{ $message['id'] }}, {{ $attachment->id }})" href="javascript:void(0)" class="nav-link text-decoration-underline">{{$attachment['original']}}</a>
                                                                    </p>
                                                                @endif
                                                                @if (empty($message['message']))
                                                                    @if($message['from_role'] === 'admin')
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
                            <div class="send-box">
                                <form wire:submit="send" class="mb-0 d-flex gap-3">
                                    <textarea wire:model="message" id="message" cols="30" rows="1" class="form-control @error('message') 'invalid-feed' @enderror" placeholder="Type Something..."></textarea>
                                    <button type="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button>
                                </form>
                                <div class="error-field">
                                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="send-btns mt-4">
                                    <div class="attach">
                                        <div class="button-wrapper">
                                            <span class="label text-uppercase fw-bold">Upload attachments</span>
                                            <input type="file" wire:model="attachments" multiple id="upload" class="upload-box" placeholder="Upload File" aria-label="Upload File">
                                        </div>
                                        
                                        <div class="attachment-grid mt-4">
                                            @if (isset($preview_attachments))
                                                @foreach($preview_attachments as $attachment)
                                                    <div class="attachment-item">
                                                        @if ($attachment['type'] === 'image')
                                                            <img src="{{ $attachment['url'] }}" class="img-fluid" alt="Preview Image" style="height: 100%; width: 100%; object-fit: scale-down;">
                                                        @elseif ($attachment['type'] === 'pdf')
                                                            <iframe src="{{ $attachment['url'] }}" width="100%" height="100%"></iframe>
                                                        @endif
                                                    </div>
                                                @endforeach
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
</div>
