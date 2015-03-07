 $(function () {
 $('#contest-match-form-submit').on('click', function(e){
        // We don't want this to act as a link so cancel the link action
        e.preventDefault();
        // Find form and submit it
        $('#contest-match-form-submit-modal').submit();
    });
    //var winner = $select.val();
    //id = $activity.attr('id').replace('activity-comment-', '');
    var param = {
                winner:$('#winner').val(),
                point:$('#point').val()
    };
    Linko.ajax.post('contest/playContest', param, function(response) {
            var $af = $('#add-user-error');
            $af.html('');
            if(response.error){			
                for(var i = 0; i < response.error.length; i++){
                    $af.append('<div class="alert alert-error">' + response.error[i] + '</div>');
                }
            }else{
                $("#addUserForm").fadeOut("fast", function(){
                    $(this).before("<strong>The user has been added to the database</strong>");
                    setTimeout("#AddUserModal.close()", 2000);
                });
            }
    }); 
    $('#contest-match-form-submit-modal').bind('hidden', function () {
        location.reload(true);
    });
});