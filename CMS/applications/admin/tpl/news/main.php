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
                <strong><?php echo _t("Name"); ?></strong>
            </td>
            <td width="100px" class="border-bottom">
            </td>
            <td class="border-bottom">
                <strong><?php echo _t("Created"); ?></strong>
            </td>
            <td width="100px" class="border-bottom">
                <strong><?php echo _t("By"); ?></strong>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("news");
        $db->setColPrefix("news_");
        $db->setSort("news_added DESC");
        $db->select();
        while ($db->nextRecord()) {

            $user = new Acl($db->userid);
            ?>
            <tr>
                <td class="border-bottom">
                    <a href="<?php echo page("admin", "news", "edit", "", "", "id=" . $db->id); ?>"><?php echo htmlformat($db->subject, false) ?></a>
                </td>
                <td class="border-bottom border-right" align="center">
                    <a href="<?php echo page("admin", "news", "edit", "", "", "id=" . $db->id); ?>"><img src="images/icons/edit_16.png" class="rel" title="<?php echo _t("Edit"); ?>" /></a>
                    <a href="<?php echo page("admin", "news", "delete", "", "", "id=" . $db->id . "&confirm"); ?>"><img src="images/icons/trash_16.png" class="rel" title="<?php echo _t("Delete"); ?>" /></a>
                </td>
                <td class="border-bottom border-right">
                    <?php echo get_date($db->added, "", 1) ?>
                </td>
                <td class="border-bottom">
                    <a href="<?php echo page("profile", "view", $user->name); ?>"><?php echo $user->name; ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>