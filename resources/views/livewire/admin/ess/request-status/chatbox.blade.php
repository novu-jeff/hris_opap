<div class="modal-dialog-scrollable" wire:click="makeSeen">
    <div class="modal-content">
        @if(!is_null($selected_id))

            @php
                $fullname = $records['user']['personal']->firstname . ' ' . $records['user']['personal']->lastname;
            @endphp
            <div class="msg-head">
                <div class="row px-5">
                    <div class="col-8">
                        <div class="d-flex align-items-center">
                            <span class="chat-icon"><img class="img-fluid" src="https://mehedihtml.com/chatbox/assets/img/arroleftt.svg" alt="image title"></span>
                            <td class="text-center">
                                <img style="width: 50px; height: 50px;"
                                    src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($fullname) }}">              
                            </td>
                            <div class="flex-grow-1 ms-3">
                                <h3>{{ (isset($records['user']) ? ucwords($fullname) : '')}}</h3>
                                <p>{{ (isset($records['user']) ? strtoupper($records['user']['positions']->name ?? 'Employee') : '')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body" wire:click="makeSeen">
                <div class="msg-body" wire:poll='loadRecords("{{$selected_id}}")' wire:poll.keep-alive>
                    <ul>
                        @if(isset($records['messages']))
                            @foreach ($records['messages'] as $message)
                                @if (!empty($message['message']) || !$message['attachments']->isEmpty())
                                    <li class="{{ $message['from_role'] === 'admin' ? 'reply' : 'sender' }} {{ !$message['attachments']->isEmpty() ? 'active' : '' }}">
                                        @if (!empty($message['message']))
                                            <p>{{ $message['message'] }}</p>
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
                        @endif
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
                            <button type="submit" class="btn btn-primary w-100">
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
        @else

        @endif
    </div>
</div>