$(function(){
    window.lastMessageText = '';
    //alert('this is twalo')
    $("#send-message-button").click(function()
    {

        //secure page through fading
        securePageForMessage();
        var div = "<div id='message-box'>";
        div += "<h1>"+Linko.translate.get('messages.send_message')+"</h1>";
        div += "<form id='send-message-form' action='#' method='post'> ";

        div += "<textarea name='text' id='message-text' >"+window.lastMessageText+"</textarea> ";
        div += "<input id='send-message-button' type='submit' value='"+Linko.translate.get('messages.send_message')+"' ><span id='message-sending-indicator-container'></span> ";
        div += "</form>";

        div += '</div>';
        $('body').prepend(div);

        $("#send-message-form").submit(function (){

            Linko.process.show('message-sending-indicator', $('#message-sending-indicator-container'));
            //alert($("#send-message-button").attr('userid'));
            var iUserId = $("#send-message-button").attr('userid');
            var messageText = $('#message-text').val();

            if(messageText == '')
            {
                Linko.notify.show(Linko.translate.get('messages.send_error'),{type:'toast'});
                Linko.process.remove('message-sending-indicator')
                return false;
            }
            Linko.ajax.post('messages/send',{userid: iUserId,text: messageText},function(data){
                hideMessageBox();
                //alert(data);
                Linko.notify.show(Linko.translate.get('messages.sent_message'),{type:'flash'});
                window.lastMessageText = '';
            },'html')

            return false;
        })

    });
    function securePageForMessage()
    {
        $('body').prepend("<div id='message-page-secure'></div> ");
        $("#message-page-secure").click(function ()
        {

                hideMessageBox();
        })

    }

    function hideMessageBox()
    {
        window.lastMessageText = $("#message-text").val();
        $('#message-page-secure').remove();
        $('#message-box').remove();
        //alert('exit');

    }
})