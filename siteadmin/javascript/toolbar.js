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
        unmarkText(".menu");
        var menu = $(this).attr("rel");
        if($("#" + menu).is(":visible")){
            unmarkText($(this));
            $("#" + menu).fadeOut(200);
        }else{
            $(".dropdown").hide();
            markText($(this));
            $("#" + menu).fadeIn(200);
        }
    });

});

function markText(targetObject)
{
    //$(targetObject).addClass("text_selected");
    $(targetObject).css("color", "#46FF00");
	
}

function unmarkText(targetObject)
{
    //$(targetObject).removeClass("text_selected");
    $(targetObject).css("color", "#BABABA");
	
}