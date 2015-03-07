$(function(){

    if($("#message-mini-container").html() != '')
    {
        $('#message-notification-link').click(function()
        {
            $("#message-mini-container").slideToggle('show');
            return false;
        })
    }
})