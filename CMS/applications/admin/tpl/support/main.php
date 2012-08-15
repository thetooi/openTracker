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

<h4>Support tickets</h4>
<table class="forum" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <td class="border-bottom">Subject</td>
            <td class="border-bottom border-right" width="17px;"></td>
            <td class="border-bottom border-right">Status</td>
            <td class="border-bottom border-right">Owner</td>
            <td class="border-bottom" width="200px;">Last reply</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("support");
        $db->setSort("ticket_added DESC, ticket_status ASC");
        $db->select();
        while ($db->nextRecord()) {
            switch ($db->ticket_status) {
                default:
                    $status = "<font color='red'>Unsolved</font>";
                    break;

                case 1:
                    $status = "<font color='green'>Solved</font>";
                    break;
            }

            $last = new DB("support_messages");
            $last->setSort("message_added DESC");
            $last->setLimit(1);
            $last->select("message_ticket = '" . $db->escape($db->ticket_id) . "'");
            $last->nextRecord();
            $last_user = new Acl($last->message_user);
            $user = new Acl($db->ticket_user);
            ?>
            <tr>
                <td class="border-bottom"><a href="<?php echo page("admin", "support", "view", "", "", "ticket=" . $db->ticket_id); ?>"><?php echo htmlformat($db->ticket_subject); ?></td>
                <td class="border-bottom border-right">
                    <a href="<?php echo page("admin", "support", "delete", "", "", "ticket=" . $db->ticket_id."&confirm") ?>" class="rel" title="<?php echo _t("Delete ticket") ?>"><img src="images/icons/trash_16.png" alt="delete" /></a>
                </td>
                <td class="border-bottom border-right"><?php echo $status; ?></td>
                <td class="border-bottom border-right"><a href="<?php echo page("profile", "view", $user->name) ?>"><?php echo $user->name; ?></a></td>
                <td class="border-bottom"><a href="<?php echo page("profile", "view", $last_user->name) ?>"><?php echo $last_user->name . "</a> " . _t("replied") . " " . get_date($last->message_added); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>