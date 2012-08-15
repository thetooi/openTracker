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

<table width="100%" class="forum" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td class="border-bottom">
                <strong>Name</strong>
            </td>
            <td width="100px" class="border-bottom">
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("groups");
        $db->setColPrefix("group_");
        $db->setSort("group_id ASC");
        $db->select();
        while ($db->nextRecord()) {
            ?>
            <tr>
                <td class="border-bottom">
                    <a href="<?php echo page("admin", "groups", "edit", "", "", "id=" . $db->id); ?>"><?php echo htmlformat($db->name, false) ?></a>
                </td>
                <td class="border-bottom border-right" align="center">
                    <a href="<?php echo page("admin", "groups", "edit", "", "", "id=" . $db->id); ?>"><img src="images/icons/edit_16.png" class="rel" title="<?php echo _t("Edit"); ?>" /></a>
                    <a href="<?php echo page("admin", "groups", "delete", "", "", "id=" . $db->id . "&confirm"); ?>"><img src="images/icons/trash_16.png" class="rel" title="<?php echo _t("Delete"); ?>" /></a>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>