var ajaxErrors = "";
var ajaxErrorCount = 0;

function alert(msg, setPrefix){
    if (setPrefix == null){
	
        $.prompt(msg);
		
    }else{
	
        $.prompt(msg, {
            prefix: setPrefix
        });
    }
}
function parseAjaxMsg(msg){
    returnValue	= msg.substr(6, (msg.length - 6));
    return returnValue;
	
}

if (typeof console == "undefined" || typeof console.log == "undefined"){
	
    var console = {
        log: function() {}
    };

}

function clog(msg){
    var d = new Date;
    var month = new Array(12);
    month[0] = "01";
    month[1] = "02";
    month[2] = "03";
    month[3] = "04";
    month[4] = "05";
    month[5] = "06";
    month[6] = "07";
    month[7] = "08";
    month[8] = "09";
    month[9] = "10";
    month[10] = "11";
    month[11] = "12";
	
    var minute = ( d.getMinutes() < 10 ) ? "0" + d.getMinutes() : d.getMinutes();
    var second = ( d.getSeconds() < 10 ) ? "0" + d.getSeconds() : d.getSeconds();
	
    var time = d.getFullYear() + "-" + month[d.getMonth()] + "-" + d.getDate() + " " + d.getHours() + ":" + minute + ":" + second + "." + d.getMilliseconds();
	
    console.log("[" + time + "] " + msg);
	
}

function ajaxError(text, xhr){

    ajaxErrorCount++;
	
    ajaxErrors = ajaxErrors + "#" + ajaxErrorCount + "<br />";
    ajaxErrors = ajaxErrors + xhr.status + ", " + xhr.statusText + "<br />";
    ajaxErrors = ajaxErrors + text + "<br />"; 
    ajaxErrors = ajaxErrors + xhr.responseText;
    ajaxErrors = ajaxErrors + "<br />";
    
    msgForLog = xhr.status + ", " + xhr.statusText + "::" + text;
   
    clog(msgForLog);
    alert(msgForLog)
}

$(document).ajaxError( function(XMLHttpRequest, textStatus, errorThrown){
    ajaxError(textStatus.status + ", " + textStatus.statusText, XMLHttpRequest);
});

$(document).ajaxStart( function() {
//    if ( PHP_SITE_LIVE == 0 ){
//        clog("ajaxStart");
//    }
});

$(document).ajaxComplete( function (event, xhr, ajaxOptions) {
    response = xhr.responseText;
    if ( response.substr(0,6) !== "AJAXOK")
    {
        var msg = "PHP error occured during ajax call in<br />URL: " + ajaxOptions.url + "<br /><br />" + response;
        if (PHP_SITE_LIVE == 0 ){	
            alert(msg);
            clog(msg);
        }	
    }
    
//    if (PHP_SITE_LIVE == 0 ){	
//        clog("ajaxComplete");
//    }
});

$(document).ajaxStop( function (msg) {
    if (ajaxErrorCount > 0){
        ajaxError = "An ajax error occurred<br /><br />" + ajaxErrors;
    }
});