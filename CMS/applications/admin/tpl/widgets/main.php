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

if(!defined("INCLUDED"))
    die("Access denied");

?>

<table id="widgets" width="100%" cellspacing="0" cellpadding="5" class="forum">
    <thead>
        <tr>
            <td class="border-bottom"><?php echo _t("Name") ?></td>
            <td class="border-bottom border-right" width="56px"></td>
            <td class="border-bottom border-right" width="150px"><?php echo _t("Widget") ?></td>
            <td class="border-bottom" width="150px"><?php echo _t("Group") ?></td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("widgets");
        $db->setSort("widget_sort ASC");
        $db->join("left", "{PREFIX}groups", "group_id", "widget_group");
        $db->select();
        while ($db->nextRecord()) {
            ?>
            <tr id="forum_<?php echo $db->widget_id ?>">
                <td class="border-bottom"><?php echo $db->widget_name; ?></td>
                <td class="border-bottom border-right">
                    <a href="<?php echo page("admin", "widgets", "edit", "", "", "id=" . $db->widget_id) ?>" class="rel" title="<?php echo _t("Edit") ?>"><img src="images/icons/edit_16.png"></a>
                    <a href="<?php echo page("admin", "widgets", "delete", "", "", "id=" . $db->widget_id . "&confirm") ?>" class="rel" title="<?php echo _t("Uninstall") ?>"><img src="images/icons/trash_16.png"></a>
                    <img src="images/icons/move_16.png" class="rel move_widget" style="cursor: move;" title="<?php echo _t("Move") ?>">
                </td>
                <td class="border-bottom border-right"><?php echo $db->widget_module ?></td>
                <td class="border-bottom"><?php echo $db->group_name; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>