$(document).ready(function(){				
    /* ---------- Add class .active to current link  ---------- */
    $('ul.main-menu li a').each(function(){	
        if($(this).hasClass('submenu')) {	
            if($($(this))[0].href==String(window.location)) {		
                $(this).parent().parent().parent().addClass('active');		
            }		
        } else {		
            if($($(this))[0].href==String(window.location)) {	
                $(this).parent().addClass('active');		
            }			
        }
    });
			
    /* ---------- Acivate Functions ---------- */
    $("#overlay").delay(1250).fadeOut(500);
    template_functions();
    widthFunctions();	
	
});

/* ---------- Numbers Sepparator ---------- */

function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
        x = x.replace(pattern, "$1.$2");
    return x;
}

/* ---------- Template Functions ---------- */		
		
function template_functions(){
	
	
    /* ---------- Submenu  ---------- */
	
    $('.dropmenu').click(function(e){

        e.preventDefault();

        $(this).parent().find('ul').slideToggle();
	
    });
	
    /* ---------- ToDo List Action Buttons ---------- */
	
    if($(".todo-list")) {
		
        $(".todo-remove").click(function(){

            $(this).parent().parent().fadeTo("slow", 0.00, function(){ //fade
                $(this).slideUp("slow", function() { //slide up
                    $(this).remove(); //then remove from the DOM
                });
            });


            return false;
        });
		
    }
	
   
	
    /* ---------- Disable moving to top ---------- */
    $('a[href="#"][data-top!=true]').click(function(e){
        e.preventDefault();
    });

    /* ---------- Datapicker ---------- */
    if($('.datepicker')) {	
        $('.datepicker').datepicker();		
    }
	
	
    /* ---------- Notifications ---------- */
    $('.noty').click(function(e){
        e.preventDefault();
        var options = $.parseJSON($(this).attr('data-noty-options'));
        noty(options);
    });

    /* ---------- Uniform ---------- */
    $("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

    /* ---------- Choosen ---------- */
    $('[data-rel="chosen"],[rel="chosen"]').chosen();

    /* ---------- Tabs ---------- */
    $('#myTab a:first').tab('show');
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    /* ---------- Makes elements soratble, elements that sort need to have id attribute to save the result ---------- */
    $('.sortable').sortable({
        revert:true,
        cancel:'.btn,.box-content,.nav-header',
        update:function(event,ui){
        }
    });

    /* ---------- Tooltip ---------- */
    $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({
        "placement":"bottom",
        delay: {
            show: 400, 
            hide: 200
        }
    });

/* ---------- Popover ---------- */
$('[rel="popover"],[data-rel="popover"]').popover();

$('.btn-close').click(function(e){
    e.preventDefault();
    $(this).parent().parent().parent().fadeOut();
});
$('.btn-minimize').click(function(e){
    e.preventDefault();
    var $target = $(this).parent().parent().next('.box-content');
    if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
    else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
    $target.slideToggle();
});
$('.btn-setting').click(function(e){
    e.preventDefault();
    $('#myModal').modal('show');
});
	
			
}

/* ---------- Page width functions ---------- */

$(window).bind("resize", widthFunctions);

function widthFunctions( e ) {
    var winHeight = $(window).height();
    var winWidth = $(window).width();

    if (winHeight) {
		
        $("#content").css("min-height",winHeight);
		
    }
    
    if (winWidth < 980 && winWidth > 767) {
		
        if($(".main-menu-span").hasClass("span2")) {
			
            $(".main-menu-span").removeClass("span2");
            $(".main-menu-span").addClass("span1");
			
        }
		
        if($("#content").hasClass("span10")) {
			
            $("#content").removeClass("span10");
            $("#content").addClass("span11");
			
        }
		
		
        $("a").each(function(){
			
            if($(this).hasClass("quick-button-small span1")) {

                $(this).removeClass("quick-button-small span1");
                $(this).addClass("quick-button span2 changed");
			
            }
			
        });
		

		
        $(".box").each(function(){
			
            var getOnTablet = $(this).attr('onTablet');
            var getOnDesktop = $(this).attr('onDesktop');
			
            if (getOnTablet) {
			
                $(this).removeClass(getOnDesktop);
                $(this).addClass(getOnTablet);
			
            }
			  			
        });
							
    } else {
		
        if($(".main-menu-span").hasClass("span1")) {
			
            $(".main-menu-span").removeClass("span1");
            $(".main-menu-span").addClass("span2");
			
        }
		
        if($("#content").hasClass("span11")) {
			
            $("#content").removeClass("span11");
            $("#content").addClass("span10");
			
        }
		
        $("a").each(function(){
			
            if($(this).hasClass("quick-button span2 changed")) {

                $(this).removeClass("quick-button span2 changed");
                $(this).addClass("quick-button-small span1");
			
            }
			
        });
		

		
        $(".box").each(function(){
			
            var getOnTablet = $(this).attr('onTablet');
            var getOnDesktop = $(this).attr('onDesktop');
			
            if (getOnTablet) {
			
                $(this).removeClass(getOnTablet);
                $(this).addClass(getOnDesktop);
			
            }
			  			
        });
		
    }

}