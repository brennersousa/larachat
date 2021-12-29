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
                        <li class="nav-item users" data-user="{{ $user->id }}"
                            data-url="{{ route('chat.chat', $user->id) }}">
                            <div class="thumb-photo rounded-circle"
                                style="background-image: url('{{ asset('assets/images/profile-photo.png') }}')"></div>
                            <div class="px-3">
                                {{ $user->first_name . ' ' . $user->last_name }}
                            </div>

                            <div
                                class="pe-3 d-flex justify-content-end col text-end {{ !isset($messagesNotRead[$user->id]) ? 'hidden-notification' : '' }}">
                                <div class="has-message">
                                    {{ isset($messagesNotRead[$user->id]) ? $messagesNotRead[$user->id] : 0 }}</div>
                            </div>
                        </li>
                    @endforeach
                </div>
            </ul>
        </div>

        <div class="window-chat col chat overflow-hidden">

            <div class="chat initial-dashboard"
                style="background-image: url('{{ asset('assets/images/dashboard.jpg') }}')"></div>
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
        $(function() {

            /**
            * activate the button to select an emoji
            * @version 1.0.0 - 20211229
            * @author Brenner S. Barboza
            * @return void
            */
            function activeEmoji(){
                $('.window-chat').find("#emoji").emojioneArea({
                    inline: true,
                    pickerPosition: 'top',
                    events: {
                        emojibtn_click: function () {
                            let actElem = $("#message");
                            let value = actElem.html() + emojione.toImage(this.getText());
                            // value = emojione.toShort(value);
                            this.setText('');
                            actElem.html(value);
                        },
                        keyup: function(){
                            this.setText('');
                        },
                    }
                });
            }

            /**
            * scroll message
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            function scrollMessage() {
                $('.chat-content').animate({
                    scrollTop: $(".chat-content").prop('scrollHeight')
                }, 200);
            }

            /**
            * load more messages
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            function loadMoreMessages() {

                $("#chat-content").on("scroll", function() {

                    if ($(this).prop('scrollTop') == 0) {
                        let userId = $(".window-chat").find('.chat').first().attr('user');
                        let firstMessage = $(this).find("[data-id").first();
                        let messageId = firstMessage.data('id');

                        if(messageId === false || firstMessage.hasClass('j_last_message')){
                            return;
                        }

                        let url = "{{ route('chat.getMessages', ['user' => '__userId__', 'lastId' => '__lastId__']) }}".replace('__userId__', userId);
                        url = url.replace('__lastId__', messageId);

                        $.ajax({
                            url: url,
                            type: "GET",
                            dataType: "JSON",
                            success: function(response) {
                                let chatBase = $(".window-chat").find('.chat').first();

                                if (response.messages && response.messages.length > 0) {
                                    for (let i in response.messages) {
                                        let message = response.messages[i];
                                        let date = new Date(message.created_at);

                                        if (message.from_user == userId) {
                                            showMessageFromUserInThechat(chatBase, message, date, true);
                                            continue;
                                        }

                                        showMessageToUserInThechat(chatBase, message.message, date, true, message.id);
                                    }
                                }else{
                                    firstMessage.addClass("j_last_message");
                                }

                                $('.window-chat').html(chatBase);
                                $('.chat-content').scrollTop(firstMessage.position().top);
                                loadMoreMessages();
                                markMessagesAsRead();
                            }
                        });
                    }
                });
            }

            /**
            * open chat when click an user
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            $("[data-user]").on('click', function(e) {
                var data = $(this).data();

                $.ajax({
                    url: data.url,
                    type: "GET",
                    dataType: "JSON",
                    beforeSend: function() {
                        let ajaxLoad = $(".chat_load").first().clone();
                        $('.window-chat').html(ajaxLoad.show());
                    },
                    success: function(response) {
                        let chatBase = $(".chat_base").first().clone();
                        if (response.user) {
                            let name = response.user['first_name'] + ' ' + response.user[
                                'last_name'];
                            chatBase.find('.j_user_name').text(name);
                            chatBase.find('.chat').first().attr('user', response.user['id']);
                        }

                        if (response.messages) {
                            for (let i in response.messages) {
                                let message = response.messages[i];
                                let date = new Date(message.created_at);

                                if (message.from_user == data.user) {
                                    showMessageFromUserInThechat(chatBase, message, date);
                                    continue;
                                }
                                showMessageToUserInThechat(chatBase, message.message, date, false, message.id);
                            }
                        }
                        $('.window-chat').html(chatBase.html()).find("[contenteditable='true']").focus();
                        scrollMessage();
                        activeEmoji();
                        loadMoreMessages();
                        markMessagesAsRead();
                    }
                });
            });


            /**
            * send new message when click the icon
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            $(".window-chat").on('click', '.j_send_message', function() {
                sendMessage();
            });

            /**
            * send new message
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            function sendMessage() {
                let chat = $(".window-chat").find("#message");
                let userId = $(".window-chat").find('.chat').first().attr('user');
                let url = "{{ route('chat.sendMessage', '__id__') }}".replace('__id__', userId);
                let message = chat.text();
                let windowChat = $(".window-chat");
                if (message.trim() == '' && $(chat).find('.emojione').length == 0) {
                    return;
                }

                message = chat.html();
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
                    success: function(response) {
                        if (!response.success) {
                            window.ajaxMessage(response.message, 5);
                            lastMessage.find('.message-to').css('background-color', '#d94352');
                        }
                    },
                    error: function() {
                        window.ajaxMessage(window.ajaxResponseRequestError, 5);
                        lastMessage.find('.message-to').css('background-color', '#d94352');
                    }
                });
            }

            /**
            * show message from user in the chat
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            function showMessageFromUserInThechat(chatElement, objectMessage, date, prepend = false) {
                let messageFrom = $('.message_from_base').first().clone();
                messageFrom.find('.message-from').attr('data-id', objectMessage.id)
                    .attr('data-message-read', objectMessage.receive_message != null ? true : false)
                    .find('.message-hour').text(date.toLocaleString('pt-BR'))
                    // .parent().prepend(document.createTextNode(objectMessage.message));
                    .parent().prepend(objectMessage.message);

                if (prepend) {
                    chatElement.find('.chat-content').prepend(messageFrom.html());
                    return;
                }
                chatElement.find('.chat-content').append(messageFrom.html());
            }

            /**
            * show message to user in the chat
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            function showMessageToUserInThechat(chatElement, message, date, prepend = false, messageId = false) {
                let messageTo = $('.message_to_base').first().clone();
                messageTo.find('.message-to').attr('data-id', messageId)
                messageTo.find('.message-hour').text(date.toLocaleString('pt-BR'))
                    // .parent().prepend(document.createTextNode(message));
                    .parent().prepend(message);

                if (prepend) {
                    chatElement.find('.chat-content').prepend(messageTo.html());
                    return;
                }
                chatElement.find('.chat-content').append(messageTo.html());
            }

            /**
            * capture keyboard event on contenteditable
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            $(".window-chat").on('keydown', "[contenteditable='true']", function(e) {

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

                if (keyCode == 13) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            /**
            * mark messages as read
            * @version 1.0.0 - 20211228
            * @author Brenner S. Barboza
            * @return void
            */
            function markMessagesAsRead(updateTotalNotification = true) {
                let messages = $('.chat-content').find(".message-from[data-message-read='false']");
                let messsageIds = [];
                for (i = 0; i < messages.length; i++) {
                    messsageIds.push($(messages[i]).attr('data-id'));
                    $(messages[i]).attr('data-message-read', true)
                }

                if (messsageIds.length == 0) {
                    return;
                }

                let element = $(".users[data-user='" + $('.window-chat').find('.chat').first().attr('user') + "']")
                    .find('.has-message');
                if (element.length > 0) {
                    let total = updateTotalNotification ? parseInt(element.html()) - messsageIds.length : parseInt(
                        element.html());
                    element.html(total);
                    if (total == 0) {
                        element.parent().addClass('hidden-notification');
                    }
                }

                let url = "{{ route('chat.markMessagesAsRead') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        'messsageIds': messsageIds
                    },
                    dataType: "JSON",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {},
                });
            }

            connectPrivateChannel();

            /**
             * connect private channel
             * Obs: open a private channel with the server
             * @version 1.0.0 - 20211228
             * @author Brenner S. Barboza
             * @return void
             */
            function connectPrivateChannel() {
                window.Echo.private('user.' + {{ auth()->user()->id }}).listen('.SendMessage', (response) => {

                    let windowChat = $(".window-chat");
                    let userId = windowChat.find('.chat').first().attr('user');
                    if (response.message.from_user == userId) {
                        let date = new Date(response.message.created_at);
                        showMessageFromUserInThechat(windowChat, response.message, date);
                        scrollMessage();
                        markMessagesAsRead(false);
                        return;
                    }

                    let element = $(".users[data-user='" + response.message.from_user + "']").find(
                        '.has-message');
                    if (element.length > 0) {
                        let total = parseInt(element.html());
                        element.html(total + 1);
                        element.parent().removeClass('hidden-notification');
                    }
                });
            }
        });
    </script>
@endsection
