var Contest=
{
    matchValidator:'',
    
    suggestTeam:function(id,boxid,type)
    {
        $("#"+boxid).html(smallIndicator);
        $("#"+boxid).show();  
        
        $.ajax(
        {
            type:'GET',
            data:{action:'sport/teamSuggest',inputid:id,boxid:boxid,func:'Contest.addSuggest',type:type,text:$("#"+id).val()},
            dataType:'html',
            success:function(data)
            {
               $("#"+boxid).html(data); 
            }
        });
        
        $('body').click(function()
        {
            $("#"+boxid).hide();
        })     
    },
    addSuggest:function(id,boxid,va)
    {
       $("#"+boxid).hide();
       
       $("#"+id).val(va);
       return false; 
    },
    validateMatch:function(id)
    {
        Twalo.securePageThroughFading('white');
        if(this.matchValidator=='')
        {
            //create new matchvalidator
            $('body').append("<div id='match-validator' class='admin-small-popup'></div>");
            Twalo.positionDiv('match-validator',2);
            this.matchValidator='created';
        }
        else
        {
            $("#match-validator").show();
        }
        
        content="<h1 style='font-size:15px'>Who Won the Match</h1>";
        content+="<select id='winner'>";
        content+="<option  value='a'>Team A</option><option value='b'>Team B</option><option value='c'>Draw Match</option></select>";
        content+="<br/><br/><a href='' onclick=\"return Contest.validateNow('"+id+"')\" class='header-button'>Validate Now</a><br/><br/>";
        $("#match-validator").html(content);
        
		$(window).keydown(function(event)
        {
												
		      if(event.keyCode==27)
		      {
															      
		          $("#match-validator").hide();	
                  Twalo.unsecurePage();
		      }
												
		});
        
        return false;
    },
    validateNow:function(id)
    {
        //alert($("#winner").val());
        //alert(id);
        $.ajax({
            type:'POST',
            data:{action:'contest/validateMatch',id:id,result:$("#winner").val()},
            success:function(data)
            {
               // alert(data);
            }
        })
        
        $("#match-validator").fadeOut();
        Twalo.unsecurePage();
        $("#my-id-"+id).hide();
        return false;
    },
    endParticipation:function(id)
    {
        $("#my-end-id-"+id).hide();
        $.ajax({
            type:'POST',
            data:{action:'contest/endMatch',id:id},
            success:function(data)
            {
               // alert(data);
            }
        })
        return false;
        
    }
}