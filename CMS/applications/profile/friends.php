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

$this->setTitle("Friends");
$this->setSidebar(true);

$user = new Acl(USER_ID);

if (isset($this->args['var_a'])) {
    $notif = new notifications_main();
    try {
        switch ($this->args['var_a']) {

            case 'accept':
                $db = new DB("friends");
                $db->friend_status = 1;
                $friend_id = getID($this->args['var_b']);
                if ($friend_id)
                    $db->update("friend_receiver = '" . USER_ID . "' AND friend_sender = '" . $friend_id . "' AND friend_status='0'");
                if ($db->affectedRows()) {
                    $db = new DB("friends");
                    $db->friend_receiver = $friend_id;
                    $db->friend_sender = USER_ID;
                    $db->friend_status = 1;
                    $db->insert();
                }

                if ($db->affectedRows())
                    $notif->add($friend_id, "friend", json_encode(array("type" => "accept", "user" => USER_ID)));
                break;

            case 'decline':
                $friend_id = getID($this->args['var_b']);
                $db = new DB("friends");
                $db->delete("friend_receiver = '" . USER_ID . "' AND friend_sender = '" . $friend_id . "' AND friend_status='0'");
                if ($db->affectedRows())
                    $notif->add($friend_id, "friend", json_encode(array("type" => "decline", "user" => USER_ID)));
                break;

            case 'remove':
                $friend_id = getID($this->args['var_b']);
                $db = new DB("friends");
                $db->delete("friend_receiver = '" . $friend_id . "' AND friend_sender = '" . USER_ID . "' AND friend_status='1'");
                $db->delete("friend_receiver = '" . USER_ID . "' AND friend_sender = '" . $friend_id . "' AND friend_status='1'");
                if ($db->affectedRows())
                    $notif->add($friend_id, "friend", json_encode(array("type" => "remove", "user" => USER_ID)));
                break;

            case 'add':
                $friend_id = getID($this->args['var_b']);

                if ($friend_id == USER_ID)
                    throw new Exception("Cannot add your self as friend");

                $db = new DB("friends");
                $db->select("friend_receiver = '" . $friend_id . "' AND friend_sender = '" . USER_ID . "'");
                if (!$db->numRows()) {
                    $db->nextRecord();
                    if ($db->status == 0) {
                        $db = new DB("friends");
                        $db->friend_sender = USER_ID;
                        $db->friend_receiver = $friend_id;
                        $db->friend_status = 0;
                        $db->insert();
                        echo notice(_t("a friend request has been sent"));
                    } else {
                        echo error(_t("this user is already your friend"));
                    }
                } else {
                    echo error(_t("a pending friend request already exist"));
                }
                break;
        }
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}

$db = new DB("friends");
$db->select("friend_receiver = '" . USER_ID . "' AND friend_status = '0'");
$friends = "";
if ($db->numRows()) {
    ?>
    <h4><?php echo _t("Pending") ?></h4>
    <div style="float:left; width: 100%;">
        <?php
        while ($db->nextRecord()) {
            $acl = new Acl($db->friend_sender);

            $time = time() - 200;
            $online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
            ?>
            <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
                <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
                <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>) <?php echo $online; ?>
                <br />
                <?php if (!$acl->anonymous || $user->Access("x")) { ?>
                    <img src="images/icons/up.gif" /> <?php echo $acl->uploaded(); ?><br />
                    <img src="images/icons/down.gif" /> <?php echo $acl->downloaded(); ?><br />
                <?php } ?>
                <br />
                <a href="<?php echo page("profile", "friends", "accept", $acl->name) ?>"><span class="btn"><?php echo _t("Accept") ?></span></a> 
                <a href="<?php echo page("profile", "friends", "decline", $acl->name) ?>"><span class="btn red"><?php echo _t("Decline") ?></span></a>
            </div>

            <?php
        }
        echo "</div>";
    }
    $db = new DB("friends");
    $db->select("friend_receiver = '" . USER_ID . "' AND friend_status = '1'");
    $friends = "";
    ?>
    <h4><?php echo _t("my friends") ?></h4>
    <?php
    if ($db->numRows()) {
        while ($db->nextRecord()) {
            $acl = new Acl($db->friend_sender);

            $time = time() - 200;
            $online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
            ?>
            <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
                <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
                <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>) <?php echo $online; ?>
                <br />
                <?php if (!$acl->anonymous || $user->Access("x")) { ?>
                    <img src="images/icons/up.gif" /> <?php echo $acl->uploaded(); ?><br />
                    <img src="images/icons/down.gif" /> <?php echo $acl->downloaded(); ?><br />
                <?php } ?>
                <br />
                <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>"><span class="btn"><?php echo _t("PM") ?></span></a> 
                <a href="<?php echo page("profile", "view", $acl->name) ?>"><span class="btn"><?php echo _t("profile") ?></span></a>
                <a href="<?php echo page("profile", "friends", "remove", $acl->name) ?>" style="float:right;"><span class="btn red"><?php echo _t("Remove") ?></span></a>
            </div>

            <?php
        }
    } else {
        echo "<center>" . _t("No friends was found") . "</center>";
    }
    ?>