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

<table class="forum" width="100%" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td class="border-bottom" width="24px"></td>
            <td class="border-bottom">
                <?php echo _t("Name") ?>
            </td>
            <td class="border-bottom border-right">
            </td>
            <td class="border-bottom">
                <?php echo _t("Installed") ?>
            </td>
        </tr>
    </thead>
    <?php
    $db = new DB("system_languages");
    $db->setSort("language_name ASC");
    $db->select();
    while ($db->nextRecord()) {
        ?>
        <tr>
            <td class="border-bottom" width="24px" align="center">
                <img src="images/flags/<?php echo $db->language_flag ?>">
            </td>
            <td class="border-bottom">
                <?php echo $db->language_name ?>
            </td>
            <td class="border-bottom border-right" width="36px">
                <?php if ($db->language_id != "en") { ?>
                    <a href="<?php echo page("admin", "translations", "edit", $db->language_id); ?>" class="rel" title="<?php echo _t("Edit") ?>"><img src="images/icons/edit_16.png"></a>
                    <a href="<?php echo page("admin", "translations", "delete", "", "", "id=" . $db->language_id . "&confirm"); ?>" class="rel" title="<?php echo _t("Delete") ?>"><img src="images/icons/trash_16.png"></a>
                <?php } ?>
            </td>
            <td class="border-bottom">
                <?php echo get_date($db->language_installed); ?>
            </td>
        </tr>
        <?php
    }
    ?>
    <tbody>
    </tbody>
</table>