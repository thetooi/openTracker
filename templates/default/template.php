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

$acl = new Acl(USER_ID);
$wpref = new Pref("website");
$spref = new Pref("system");
?>
<div id="wrapper">
    <div id="topbar">
        <?php
        if (!$this->login) {
            ?>
            <div id="actions">
                <ul id="menu">
                    <?php
                    $db = new DB("messages");
                    $db->select("message_receiver = '" . USER_ID . "' AND message_unread = 'yes'");
                    $mail = "";
                    if ($db->numRows())
                        $mail = "<span class='msgAlert'>" . $db->numRows() . "</span>";

                    $db = new DB("friends");
                    $db->select("friend_receiver = '" . USER_ID . "' AND friend_status = '0'");
                    $friends = "";
                    if ($db->numRows())
                        $friends = "<span class='msgAlert'>" . $db->numRows() . "</span>";

                    $sup = new DB("support");
                    $sup->select("ticket_user = '" . USER_ID . "'");
                    $count = 0;
                    while ($sup->nextRecord()) {
                        $db = new DB("support_messages");
                        $db->select("message_ticket = '" . $sup->ticket_id . "' AND message_unread = '0'");
                        $count += $db->numRows();
                    }

                    $support = "";
                    if ($count > 0) {
                        $support = "<span class='msgAlert'>" . $count . "</span>";
                    }
                    ?>

                    <li class="rel" title="<?php echo _t("Mailbox") ?>">
                        <a href="<?php echo page("profile", "mailbox"); ?>">
                            <img src="images/icons/mailbox.png" alt="mailbox" />
                            <?php echo "$mail"; ?></a></li>

                    <li class="rel" title="<?php echo _t("Friends") ?>">
                        <a href="<?php echo page("profile", "friends"); ?>"><img src="images/icons/friends.png" alt="friends"  >
                            <?php echo "$friends"; ?></a></li>

                    <li class="rel" title="<?php echo _t("RSS Generator") ?>">
                        <a href="<?php echo page("rss"); ?>"><img src="images/icons/rss.png" alt="rss" /></a></li>
                    <?php if ($acl->uploader) { ?>
                        <li class="rel" title="<?php echo _t("Upload") ?>">
                            <a href="<?php echo page("torrent", "upload"); ?>"><img src="images/icons/upload.png" alt="upload" /></a></li>
                    <?php } ?>
                    <li class="rel" title="<?php echo _t("Help & Support") ?>"><a href="<?php echo page("support"); ?>"><img src="images/icons/support.png" alt="admin" /><?php echo $support ?></a></li>
                    <li class="rel" title="<?php echo _t("Logout") ?>"><a href="<?php echo page("user", "logout"); ?>"><img src="images/icons/logout.png" alt="logout" /></a></li>
                </ul>
            </div>
            <div id="user">
                <?php echo _t("Welcome"); ?>
                <a href="<?php echo page("profile") ?>" title="Go to profile"><strong><?php echo $acl->name; ?></strong></a>
                (<?php echo $acl->group_name; ?>) 
            </div>
            <div id="info"> 
                <?php echo _t("Ratio"); ?>: <?php echo $acl->ratio() ?>
                <img src="images/icons/up.gif" style="padding-left: 10px;" alt="up" /> <?php echo $acl->uploaded(); ?>
                <img src="images/icons/down.gif" style="padding-left: 10px;" alt="down" /> <?php echo $acl->downloaded(); ?> 
            </div>
            <?php
        }
        ?>
    </div>
    <div id="header">
        <div id="header_content">
            <img src="images/logo.png" alt="logo" />
        </div>
    </div>
    <div id="navi_top_wrapper">
        <?php
        if (!$this->login) {
            ?>
            <ul id="navi_top">
                <?php
                $nav = new Navigation($this->data['url']['application'], $this->data['url']['action']);
                echo $nav->build();
                ?>
            </ul>
            <?php
        } else {
            ?>
            <ul id="navi_top">
                <li><a href="<?php echo page("user", "login"); ?>"><?php echo _t("Log in") ?></a></li>
                <?php
                if ($spref->registration) {
                    ?>
                    <li><a href="<?php echo page("user", "register"); ?>"><?php echo _t("Sign up") ?></a></li>
                <?php }
                ?>
                <li><a href="<?php echo page("user", "recover"); ?>"><?php echo _t("Recover my account") ?></a></li>
            </ul>
        <?php }
        ?>
    </div>
    <div id="content_wrapper">
        <?php
        if ($this->sidebar) {
            ?>
            <div id="sub_content">
                <?php echo $this->sub_content; ?>
            </div>
        <?php }
        ?>
        <div id="main_content">
            <?php
            echo $this->content;
            ?>
        </div>
    </div>
    <div id="footer">
        <div id="footer_content">
            <?php echo htmlformat($wpref->footer, true); ?>
        </div>
    </div>
</div>