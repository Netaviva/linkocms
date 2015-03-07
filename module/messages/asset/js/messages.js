$(function ()
{
    //auto scroll conversation div to the bottom
    $("#main-messages-conversation-div").scrollTop($("#main-messages-conversation-div")[0].scrollHeight);
    $("#main-messages-conversation-div,#messages-entity-wrapper").mouseover(function()
        {
            $(this).css('overflow','auto');
        });
    $("#main-messages-conversation-div,#messages-entity-wrapper").mouseout(function()
    {
        $(this).css('overflow','hidden');
    })

    $("#message-post-reply-form").submit(function()
    {
        Linko.process.show('message-reply-indicator', $('#message-post-reply-indicator'));
        var iUserId = $("#from-user-id").val();

        //alert(iUserId);
        var messageText = $('#message-text').val();

        if(messageText == '')
        {
            Linko.notify.show(Linko.translate.get('messages.send_error'),{type:'toast'});
            Linko.process.remove('message-reply-indicator')
            return false;
        }
        Linko.ajax.post('messages/send',{userid: iUserId,text: messageText},function(data){

            //alert(data);
            Linko.notify.show(Linko.translate.get('messages.sent_message'),{type:'flash'});
            Linko.process.remove('message-reply-indicator');
            $('#message-text').val('');
            $("#main-messages-conversation-div").append(data).scrollTop($("#main-messages-conversation-div")[0].scrollHeight);


        },'html');

        return false;
    });

    $('.message-entity-link').each(function()
    {
        $(this).click(function(){


            $('#main-messages-conversation-div').html('');
            Linko.process.show('message-entity-change-indicator', $('#main-messages-conversation-div'));
            var userid =  $(this).attr('userid');

            //alert(userid);
            if(userid == '0')
            {
                $('#message-post-reply-form').fadeOut();
            }
            else
            {
                $('#message-post-reply-form').fadeIn();
            }
            //hide the conversation container for another effect

            //alert(userid);
            $("#from-user-id").val(userid);
            Linko.ajax.get('messages/entityMessages', {userid: userid},function(data)
            {
                //alert(data);
                $('#main-messages-conversation-div').hide();
                $('#main-messages-conversation-div').html(data);
                $('#main-messages-conversation-div').slideToggle('');
            });
            return false;
        })
    });

    //auto hide the post form if the user id is 0
    var iUserId = $("#from-user-id").val();
    if(iUserId == 0)
    {
        $('#message-post-reply-form').fadeOut();
    }
});