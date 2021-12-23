@extends('master.master')

@section('content')
    <div class="row">
        <div class="users-list col-3">
            <div class="user-list-header py-2 px-2 d-flex">
                <div class="col">
                    <div class="thumb-photo rounded-circle"
                        style="background-image: url('{{ asset('assets/images/profile-photo.png') }}')"></div>
                </div>
                <div class="col text-end px-3" id="settings" data-bs-toggle="dropdown" aria-expanded="false"><i
                        class="fas fa-ellipsis-v"></i></div>
                <ul class="dropdown-menu" aria-labelledby="settings">
                    <li><a class="dropdown-item" href="{{ route('chat.logout') }}">Sair</a></li>
                </ul>
            </div>
            <ul class="nav flex-column users py-2 ps-2">
                <div class="scroller">
                    @foreach ($users as $user)
                        <li class="nav-item users" data-user="{{ $user->id }}" data-url="{{ route('chat.chat', $user->id) }}">
                            <div class="thumb-photo rounded-circle"
                                style="background-image: url('{{ asset('assets/images/profile-photo.png') }}')"></div>
                            <div class="px-3">
                                {{ $user->first_name . ' ' . $user->last_name }}
                            </div>
                        </li>
                    @endforeach
                </div>
            </ul>
        </div>

        <div class="window-chat col chat overflow-hidden">
            
            <div class="chat initial-dashboard" style="background-image: url('{{ asset('assets/images/dashboard.jpg') }}')"></div>
        </div>
    </div>

    <!-- CHAT BASE -->
    <div class="chat_base" style="display: none">
        @include('includes.chat')
    </div>
    <!-- END: CHAT BASE -->

    <!-- CHAT LOAD -->
    <div class="chat_load" style="display: none">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
        </div>
    </div>
    <!-- END: CHAT LOAD -->

    <!-- MESSAGE FROM -->
    <div class="message_from_base" style="display: none">
        @include('includes.message-from')
    </div>
    <!-- END: MESSAGE FROM -->
    
    <!-- MESSAGE TO -->
    <div class="message_to_base" style="display: none">
        @include('includes.message-to')
    </div>
    <!-- END: MESSAGE TO -->

    <!-- AJAX RESPONSE -->
    @include('includes.ajax-response')
    <!-- END: AJAX RESPONSE -->
@endsection

@section('scripts')
<script>
    $(function(){
        function scrollMessage(){
            $('.chat-content').animate({
                scrollTop: $(".chat-content").prop('scrollHeight')
            }, 200);
        }

        $("[data-user]").on('click', function(e){
            var data = $(this).data();

            $.ajax({
                url: data.url,
                type: "GET",
                dataType: "JSON",
                beforeSend: function () {
                    let ajaxLoad = $(".chat_load").first().clone();
                    $('.window-chat').html(ajaxLoad.show());
                },
                success: function (response) {
                    let chatBase = $(".chat_base").first().clone();
                    if(response.user){
                        let name = response.user['first_name'] + ' ' + response.user['last_name'];
                        chatBase.find('.j_user_name').text(name);
                        chatBase.find('.chat').first().attr('user', response.user['id']);
                    }

                    if(response.messages){
                        for(let i in response.messages){
                            let message = response.messages[i]; 
                            let date =  new Date(message.created_at);

                            if(message.from_user == data.user){
                                // let messageFrom = $('.message_from_base').first().clone();
                                // messageFrom.find('.message-hour').text(date.toLocaleString('pt-BR'))
                                // .parent().prepend(message.message);

                                // chatBase.find('.chat-content').append(messageFrom.html());

                                showMessageFromUserInThechat(chatBase, message.message, date);
                                continue;
                            }

                            // let messageTo = $('.message_to_base').first().clone();
                            //     messageTo.find('.message-hour').text(date.toLocaleString('pt-BR'))
                            //     .parent().prepend(message.message);

                            //     chatBase.find('.chat-content').append(messageTo.html());
                                showMessageToUserInThechat(chatBase, message.message, date);
                        }
                    }
                    $('.window-chat').html(chatBase.html());

                    scrollMessage();
                },
                error: function () {
                    // ajaxMessage(ajaxResponseRequestError, ajaxResponseBaseTime);
                    // load.fadeOut();
                }
            });
        });


        $(".window-chat").on('click', '.j_send_message', function(){
            sendMessage();
        });

        function sendMessage(){
            let chat = $(".window-chat").find("[contenteditable='true']");
            let userId = $(".window-chat").find('.chat').first().attr('user');
            let url = "{{ route('chat.sendMessage', '__id__') }}".replace('__id__', userId);
            let message = chat.text();
            let windowChat = $(".window-chat");
            if(message == ''){
                return;
            }
            chat.text('');
            showMessageToUserInThechat(windowChat, message, new Date());
            let lastMessage = windowChat.find('.content-message-to').last();
            
            scrollMessage();

            $.ajax({
                url: url,
                type: "POST",
                data: {'message': message},
                dataType: "JSON",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                   if(!response.success){
                        window.ajaxMessage(response.message, 5);
                        lastMessage.find('.message-to').css('background-color', '#d94352');
                   }
                },
                error: function () {
                    window.ajaxMessage(window.ajaxResponseRequestError, 5);
                    lastMessage.find('.message-to').css('background-color', '#d94352');
                }
            });
        }

        function showMessageFromUserInThechat(chatElement, message, date)
        {
            let messageFrom = $('.message_from_base').first().clone();
                messageFrom.find('.message-hour').text(date.toLocaleString('pt-BR'))
                .parent().prepend(document.createTextNode(message));

                chatElement.find('.chat-content').append(messageFrom.html());
        }

        function showMessageToUserInThechat(chatElement, message, date)
        {
            let messageTo = $('.message_to_base').first().clone();
                messageTo.find('.message-hour').text(date.toLocaleString('pt-BR'))
                .parent().prepend(document.createTextNode(message));

                chatElement.find('.chat-content').append(messageTo.html());
        }

        $(".window-chat").on('keydown', "[contenteditable='true']", function(e){

                var evt = e;
                var keyCode = evt.charCode || evt.keyCode;

                if (e.ctrlKey && e.keyCode == 13) {
                    let message = $(this).html() + '<div></br></div>';
                    $(this).html(message);

                    // move pointer to end of div
                    document.execCommand('selectAll', false, null);
                    document.getSelection().collapseToEnd();
                    return;
                } 

                if(keyCode==13){
                    e.preventDefault();
                    sendMessage();
                }
        });
    });
</script>
@endsection
