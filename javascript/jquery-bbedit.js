function addtag(elementName, tag) {
    var obj = document.getElementById(elementName);

    beforeText = obj.value.substring(0, obj.selectionStart);
    selectedText = obj.value.substring(obj.selectionStart, obj.selectionEnd);
    afterText = obj.value.substring(obj.selectionEnd, obj.value.length);

    switch(tag) {
        
        default:
            tagOpen = "["+tag+"]";
            tagClose = "[/"+tag+"]";

            newText = beforeText + tagOpen + selectedText + tagClose + afterText;
            break;
        
        case "url":
            if(selectedText == ""){
                var patternHTTP = /http:\/\//i;
                url = prompt("Enter URL \nExample: http://www.example.com", "");
            }else{
                url = selectedText;
            }
            
            
            if (url == null) {
                break;
            } else if (!url.match(patternHTTP)) {
                url = "http://"+url;
            }

            tagOpen = "[url=" + url + "]";
            tagClose = "[/url]";

            newText = beforeText + tagOpen + selectedText + tagClose + afterText;
            break;

        case "img":
            if(selectedText == ""){
                var patternHTTP = /http:\/\//i;
                imgURL = prompt("Enter image URL \nExample: http://www.example.com/image.jpg", "");
            }else{
                imgURL = selectedText;
            }
            if (imgURL == null) {
                break;
            } else if (!imgURL.match(patternHTTP)) {
                imgURL = "http://"+imgURL;
            }

            tagOpen = "[img]" + imgURL;
            tagClose = "[/img]";

            newText = beforeText + tagOpen + selectedText + tagClose + afterText;
            break;
    }
    obj.value = newText;
    obj.focus();
}

function insertText(a,b){
    $("#"+a).insertAtCaret(b);
}

(function(a){
    a.fn.insertAtCaret=function(a){
        return this.each(function(){
            if(document.selection){
                this.focus();
                sel=document.selection.createRange();
                sel.text=a;
                this.focus()
            }else if(this.selectionStart||this.selectionStart=="0"){
                var b=this.selectionStart;
                var c=this.selectionEnd;
                var d=this.scrollTop;
                this.value=this.value.substring(0,b)+a+this.value.substring(c,this.value.length);
                this.focus();
                this.selectionStart=b+a.length;
                this.selectionEnd=b+a.length;
                this.scrollTop=d
            }else{
                this.value+=a;
                this.focus()
            }
        })
    }
})(jQuery);

