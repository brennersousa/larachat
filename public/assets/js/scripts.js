
$(function() {
    window.ajaxResponseBaseTime = 5;
    window.ajaxResponseRequestError = "<div class='message error icon-warning'>Desculpe mas não foi possível processar sua requisição...</div>";


    // AJAX RESPONSE
     window.ajaxMessage = function(message, time){
        var ajaxMessage = $(message);

        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({"width": "100%"}, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
        ajaxMessage.effect("bounce");
    }

    // AJAX RESPONSE MONITOR
    $(".ajax_response .message").each(function (e, m) {
        ajaxMessage(m, ajaxResponseBaseTime += 1);
    });

    // AJAX MESSAGE CLOSE ON CLICK
    $(".ajax_response").on("click", ".message", function (e) {
        $(this).effect("bounce").fadeOut(1);
    });
});