@extends('layouts.master')

@section('content')
<div class="container" style="margin-top: 10px;">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h2 class="card-title mb-2 col-9" style="margin-top:5px">{{ __($friendInfo->name) }}</h4>
                <button type="button" class="btn btn-primary col-3" data-toggle="modal" data-target="#attachmentModal" style="float: right;">Open Attachments</button>
            </div>
        </div>
        <div class="card-body">
            <div class="chat-conversation">
                <ul class="conversation-list" data-simplebar>
                    <div id="message-container">
                        @if ($chats->count())
                        @foreach ($chats as $chat)
                        @if ($chat->sender_id == $authInfo->id)
                        <li class="clearfix">
                            <!-- <div class="chat-avatar">
                            <img src="" class="avatar-xs rounded-circle">
                            <span class="time">{{ $chat->send_at }}</span>
                        </div> -->
                            <div class="conversation-text">
                                <div class="ctext-wrap">
                                    <span class="user-name">You</span>
                                    <p>
                                        {{ $chat->mesej }}
                                    </p>
                                    <p class="small font-weight-light" style="text-align: right;">
                                        {{ date('H:i A', strtotime($chat->send_at)) }}
                                    </p>
                                </div>
                            </div>
                        </li>
                        @else
                        <li class="clearfix odd">
                            <!-- <div class="chat-avatar">
                            <img src="" class="avatar-xs rounded-circle">
                            <span class="time">{{ $chat->send_at }}</span>
                        </div> -->
                            <div class="conversation-text">
                                <div class="ctext-wrap">
                                    <span class="user-name">{{ $friendInfo->name }}</span>
                                    <p>
                                        {{ $chat->mesej }}
                                    </p>
                                    <p class="small font-weight-light" style="text-align: left;">
                                        {{ date('H:i A', strtotime($chat->send_at)) }}
                                    </p>
                                </div>
                            </div>
                        </li>
                        @endif
                        @endforeach
                        @endif
                    </div>
                </ul>
                <form id="send-container" class="row">
                    <div class="col-sm-8 col-8 chat-inputbar">
                        <input type="text" class="form-control chat-input" placeholder="Message..." id="message-input">
                    </div>
                    <div class="col-sm-2 col-2 chat-send" id="button-addon4">
                        <input class="file" type="file" id="file-input" />
                    </div>
                    <div class="col-sm-2 col-2 chat-send" id="button-addon4">
                        <button class="btn btn-success btn-block" type="submit" id="send-button">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="attachmentModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr style="text-align:center">
                            <th>Nama File</th>
                            <th>Tarikh Dihantar</th>
                        </tr>

                        @foreach($attachments as $row)
                        <tr>
                            <td><a href="{{ route('get-file', ['filename' => $row->name]) }}">{{ $row->name }}</a></td>
                            <td>{{ $row->created_at }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Modal -->

<script src="https://cdn.socket.io/socket.io-3.0.1.min.js"></script>
<script>
    const socket = io.connect('http://primchat-dev.herokuapp.com')
    const messageForm = document.getElementById('send-container')
    const messageInput = document.getElementById('message-input')
    const fileInput = document.getElementById('file-input')

    const name = '{{ auth()->user()->name }}'
    const id = '{{ $chatroom }}'
    const friendId = '{{ $friendInfo->id }}'

    socket.emit('new-user', id)

    socket.on('chat-message', data => {
        appendMessage(`${data.message}`, `${data.name}`)
    })

    messageForm.addEventListener('submit', e => {
        e.preventDefault()
        const message = messageInput.value
        appendMessage(`${message}`, 'You')
        messageInput.value = ''

        let url = "{{ route('send-message') }}"
        let formData = new FormData()
        let token = "{{ csrf_token() }}"
        let chatroom_id = "{{ $chatroom }}"
        formData.append('mesej', message)
        formData.append('chatroom_id', chatroom_id)
        formData.append('_token', token)
        formData.append('file', fileInput.files[0])
        fileInput.value = '';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function(response) {
                if (response.success) {
                    socket.emit('send-chat-message', {
                        message: message,
                        name: name,
                        to: id
                    });
                }
                else {
                    alert(`${response.message}`)
                }
            }
        });
    })

    function appendMessage(message, username) {
        if (username === "You") {
            $("#message-container").append(`<li class="clearfix">
                        <div class="conversation-text">
                            <div class="ctext-wrap">
                                <span class="user-name">` + username + `</span>
                                <p>` + message + `</p>
                                <p class="small font-weight-light" style="text-align: right;">
                                    {{ date('H:i A') }}
                                </p>
                            </div>
                        </div>
                    </li>`);
        } else {
            $("#message-container").append(`<li class="clearfix odd">
                        <div class="conversation-text">
                            <div class="ctext-wrap">
                                <span class="user-name">` + username + `</span>
                                <p>` + message + `</p>
                                <p class="small font-weight-light" style="text-align: left;">
                                    {{ date('H:i A') }}
                                </p>
                            </div>
                        </div>
                    </li>`);
        }
    }
</script>
@endsection