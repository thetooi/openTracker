<script type="text/javascript">
    $(document).ready(function(){
        
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
 
<?php
$db2 = new DB("system_languages");
$db2->setSort("language_name ASC");
$db2->select();
while ($db2->nextRecord()) {
    ?>
                $(".navigation_<?php echo $db2->language_id ?> tbody").sortable({
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
                            data: "action=sort&type=navigation&sorting="+sort
                        });
                    }
                }).disableSelection(); 
                        
<?php } ?>
    });
</script>

<?php
$db2 = new DB("system_languages");
$db2->setSort("language_name ASC");
$db2->select();
while ($db2->nextRecord()) {
    ?>
    <h4><?php echo $db2->language_name; ?></h4>
    <table width="100%" class="navigation_<?php echo $db2->language_id ?> forum" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <td class="border-bottom">
                    <strong><?php echo _t("Name"); ?></strong>
                </td>
                <td width="60px" class="border-bottom">
                </td>
                <td width="150px" class="border-bottom">
                    <strong><?php echo _t("Application"); ?></strong>
                </td>
                <td width="150px" class="border-bottom">
                    <strong><?php echo _t("Action"); ?></strong>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("navigations");
            $db->setColPrefix("navigation_");
            $db->setSort("navigation_sorting ASC");
            $db->select("navigation_lang = '" . $db->escape($db2->language_id) . "'");
            while ($db->nextRecord()) {
                ?>
                <tr id="item_<?php echo $db->id ?>">
                    <td class="border-bottom">
                        <a href="<?php echo page("admin", "navigation", "edit", "", "", "id=" . $db->id); ?>"><?php echo htmlformat($db->title, false) ?></a>
                    </td>
                    <td class="border-bottom border-right" align="center">
                        <a href="<?php echo page("admin", "navigation", "edit", "", "", "id=" . $db->id); ?>"><img src="images/icons/edit_16.png" class="rel" title="<?php echo _t("Edit"); ?>" /></a>
                        <a href="<?php echo page("admin", "navigation", "delete", "", "", "id=" . $db->id . "&confirm"); ?>"><img src="images/icons/trash_16.png" class="rel" title="<?php echo _t("Delete"); ?>" /></a>
                        <img src="images/icons/move_16.png" class="move_item rel" style="cursor: move;"  title="<?php echo _t("Move"); ?>" />
                    </td>
                    <td class="border-bottom border-right">
                        <?php echo $db->application ?>
                    </td>
                    <td class="border-bottom">
                        <?php echo $db->module == "" ? "main" : $db->module ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

<?php } ?>
