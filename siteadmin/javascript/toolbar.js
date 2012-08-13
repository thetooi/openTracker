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
    
    
    $.ajax({
        type: "POST",
        url: "siteadmin/ajaxWindowSession.php",
        data: "&action=get_open_window_sessions",
        success: function(msg){
            sessions_string = parseAjaxMsg(msg);
            sessions_arr = sessions_string.split("::");
            if (sessions_string.length > 0)
            {
                jQuery.each(sessions_arr, function () {
                    if(this != ""){
                        $("#" + this).css("display", "block");
                        markText("[rel^='"+this+"']");
                    }		
                });		
            }
            else
            {   
                if(sessions_arr != ""){
                    $("#" + sessions_arr).css("display", "block");
                    markText("[rel^='"+sessions_arr+"']");
                }
			
            }	
        }
    });
    
    
    $(".menu").live("click", function(e){
        e.preventDefault();
        unmarkText(".menu");
        var menu = $(this).attr("rel");
        if($("#" + menu).is(":visible")){
            closeAll();
            windowSession(menu);
            unmarkText($(this));
            $("#" + menu).fadeOut(200);
        }else{
            $(".dropdown").hide();
            closeAll();
            windowSession(menu);
            markText($(this));
            $("#" + menu).fadeIn(200);
        }
    });

});

function windowSession(div){
    if ($("#" + div).is(":hidden")){
        sendOpen = 1;
    } else {
        sendOpen = 0;
    }
	
    var urlCall = "siteadmin/ajaxWindowSession.php";
    $.ajax({
        type: "POST",
        global: false,
        url: urlCall,
        data: "div_id=" + div + "&open=" + sendOpen + "&action=window_session",
        success: function(msg){ 

        }
    });
}

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

function close(div){
    sendOpen = 0;
    var urlCall = "siteadmin/ajaxWindowSession.php";
    $.ajax({
        type: "POST",
        global: false,
        url: urlCall,
        data: "div_id=" + div + "&open=" + sendOpen + "&action=window_session",
        success: function(msg){ 

        }
    });
}

function closeAll(){
    var divs = [
        "admin_tools",
        "admin_members",
        "admin_settings"
    ];
    $.each(divs, function (index, value) {
        div_id = value;
        close(div_id);
    });

}