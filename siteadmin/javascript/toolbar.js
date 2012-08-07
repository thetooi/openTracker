$(document).ready(function(){
    
    var main_wrapper = "#wrapper";
    
    $(main_wrapper).css("margin-top", "37px");
    
    $(window).scroll(function (){
        admin_pos_top = $(document).scrollTop();
        admin_pos_left = 0;
        top_wrapper_top = $(document).scrollTop();

        $("#toolbar").css("top", admin_pos_top);
        $("#toolbar").css("left", admin_pos_left);
        $("#toolbar").hide();
        $("#toolbar").fadeIn(400);
    });
    
    $(".menu").live("click", function(e){
        e.preventDefault();
        var menu = "#" + $(this).attr("rel");
        if($(menu).is(":visible")){
            $(menu).fadeOut(200);
        }else{
            $(".dropdown").hide();
            $(menu).fadeIn(200);
        }
    });
    
    $('html').live('click',function(e) {
        if(!$(e.target).is(".dropdown") && !$(e.target).is(".dropdown li") && !$(e.target).is(".dropdown li img") && !$(e.target).is(".menu")){
            if($(".dropdown").is(":visible")){
                $(".dropdown").hide();
            }
        }
        
    });
});