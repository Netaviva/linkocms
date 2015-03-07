$(function()
{
    $(".activity-add-badge-button").each(function(){
        $(this).click(function(){
            $("#"+this.id+"-badge-forms").toggle('slide')
        })
    });

    $(".activity-cancel-button").each(function(){
        $(this).click(function(){
            $("#"+this.id+"-badge-forms").slideUp()
            return false;
        })
    })
});
