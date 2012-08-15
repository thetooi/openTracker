<?php
/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */
if (!defined("INCLUDED"))
    die("Access denied");

try {
    $acl = new Acl(USER_ID);
    ?>

    <script type="text/javascript">
        $(document).ready(function(){
                                    
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            };

            $(".bonus tbody").sortable({
                handle: '.move_item',
                appendTo: 'body',
                tolerance: 'pointer',
                forceHelperSize: true,
                helper: fixHelper,
                update: function(event, ui) {
                    var sort = $(this).sortable('toArray').toString();
                    $.ajax({
                        type: "POST",
                        url: PATH_APP+"ajax.php",
                        data: "action=sort&type=bonus&sorting="+sort
                    });
                }
            }).disableSelection(); 
                                                       
        });
    </script>

    <table class="forum bonus" width="70%" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <td class="border-bottom border-right"><?php echo _t("Store item"); ?></td>
                <td class="border-bottom"></td>
                <td class="border-bottom"><?php echo _t("Costs"); ?></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("bonus");
            $db->setColPrefix("bonus_");
            $db->setSort("bonus_sort ASC");
            $db->select();
            while ($db->nextRecord()) {
                ?>
                <tr id="item_<?php echo $db->id ?>">
                    <td class="border-bottom border-right">
                        <?php echo $db->title; ?><br />
                        <small><?php echo htmlformat($db->description, true) ?></small>
                    </td>
                    <td class="border-bottom border-right" width="56px" align="center">
                        <a href="<?php echo page("admin", "bonus", "edit", "", "", "id=" . $db->id) ?>" class="rel" title="Edit"><img src="images/icons/edit_16.png" /></a>
                        <a href="<?php echo page("admin", "bonus", "delete", "", "", "id=" . $db->id . "&confirm") ?>" class="rel" title="Delete"><img src="images/icons/trash_16.png" /></a>
                        <img src="images/icons/move_16.png" class="move_item" style="cursor:move;" />
                    </td>
                    <td class="border-bottom"><?php echo $db->cost; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
