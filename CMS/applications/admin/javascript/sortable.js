$(document).ready(function(){
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };
            
    $("#sortables").sortable({
        handle: '.move_cat',
        appendTo: 'body',
        tolerance: 'pointer',
        forceHelperSize: true,
        helper: 'clone',
        update: function(event, ui) {
            var sort = $(this).sortable('toArray').toString();
            $.ajax({
                type: "POST",
                url: PATH_APP+"ajax.php",
                data: "action=sort&type=cat&sorting="+sort
            });
        }
    });
                
    $("#widgets tbody").sortable({
        handle: '.move_widget',
        appendTo: 'body',
        tolerance: 'pointer',
        forceHelperSize: true,
        helper: fixHelper,
        update: function(event, ui) {
            var sort = $(this).sortable('toArray').toString();
            $.ajax({
                type: "POST",
                url: PATH_APP+"ajax.php",
                data: "action=sort&type=widget&sorting="+sort
            });
        }
    }).disableSelection();
                
    $("#forum tbody").sortable({
        handle: '.move_forum',
        appendTo: 'body',
        tolerance: 'pointer',
        forceHelperSize: true,
        helper: fixHelper,
        update: function(event, ui) {
            var sort = $(this).sortable('toArray').toString();
            $.ajax({
                type: "POST",
                url: PATH_APP+"ajax.php",
                data: "action=sort&type=forum&sorting="+sort
            });
        }
    }).disableSelection();
});