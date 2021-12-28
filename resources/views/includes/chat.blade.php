{{-- <div class="chat col" style="background-image: url('{{ asset('assets/images/chat.png') }}')"> --}}
<div class="chat" style="background-image: url('{{ asset('assets/images/chat.png') }}')">
    <div class="chat-background">

        <div class="chat-header py-2 px-2 d-flex">
            <div class="col">
                <div class="d-flex align-content-center align-items-center">
                    <div class="thumb-photo rounded-circle"
                        style="background-image: url('{{ asset('assets/images/profile-photo.png') }}')"></div>
                    <span class="bold px-2 j_user_name"></span>
                </div>
            </div>
            <div class="col text-end px-3"><i class="fas fa-ellipsis-v"></i></div>
            {{-- <div class="thumb-photo rounded-circle" style="background-image: url('{{ !empty($employee) ? $employee->getProfilePhoto() : asset('assets/admin/images/avatar-masculino.png') }}')"></div> --}}
        </div>
        <div class="chat-body">
            <div class="chat-content px-5 scroller" id="chat-content"></div>

            <div class="chat-footer py-2 px-2 d-flex justify-content-between">
                <div class="d-flex align-items-center align-content-lg-between">
                    {{-- <textarea name="message" id="message" class="custom-textarea" rows="1"></textarea> --}}
                    <div class="custom-textarea-content">
                        <div contenteditable="true" id="message" class="scroller custom-textarea"
                            title="Digite uma mensagem"></div>
                    </div>
                    <img class="icon-send-message j_send_message" src="{{ asset('assets/images/send-message.png') }}"
                        title="Enviar mensagem">
                </div>
            </div>
        </div>
    </div>
</div>