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
            <td class="border-bottom">
                Language
            </td>
            <td class="border-bottom border-right" width="35px">
            </td>
            <td class="border-bottom" width="170px">
                Last edited
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("faqs");
        $db->join("left", "{PREFIX}system_languages", "language_id", "faq_lang");
        $db->select();
        while ($db->nextRecord()) {
            ?>
            <tr>
                <td class="border-bottom">
                    <img src="images/flags/<?php echo $db->language_flag; ?>"> <?php echo $db->language_name; ?>
                </td>
                <td class="border-bottom border-right">
                    <a href="<?php echo page("admin", "documents", "edit", "faq", "", "lang=" . $db->faq_lang) ?>"><img src="images/icons/edit_16.png" /></a>
                    <a href="<?php echo page("admin", "documents", "delete", "", "", "type=faqs&id=" . $db->faq_lang . "&confirm") ?>"><img src="images/icons/trash_16.png" /></a>
                </td>
                <td class="border-bottom">
                    <?php echo get_date($db->faq_edited) ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<h4>Rules</h4>
<table class="forum" width="100%" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td class="border-bottom">
                Language
            </td>
            <td class="border-bottom border-right" width="35px">
            </td>
            <td class="border-bottom" width="170px">
                Last edited
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("rules");
        $db->join("left", "{PREFIX}system_languages", "language_id", "rule_lang");
        $db->select();
        while ($db->nextRecord()) {
            ?>
            <tr>
                <td class="border-bottom">
                    <img src="images/flags/<?php echo $db->language_flag; ?>"> <?php echo $db->language_name; ?>
                </td>
                <td class="border-bottom border-right">
                    <a href="<?php echo page("admin", "documents", "edit", "rules", "", "lang=" . $db->rule_lang) ?>"><img src="images/icons/edit_16.png" /></a>
                    <a href="<?php echo page("admin", "documents", "delete", "", "", "type=rules&id=" . $db->rule_lang . "&confirm") ?>"><img src="images/icons/trash_16.png" /></a>
                </td>
                <td class="border-bottom">
                    <?php echo get_date($db->rule_edited) ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>