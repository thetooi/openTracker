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

    $this->setSidebar(true);

    if (!isset($this->args['var_a']) && empty($this->args['var_a']))
        throw new Exception("Missing variable");

    $db = new DB("users");
    $db->select("user_name = '" . $db->escape($this->args['var_a']) . "'");

    if (!$db->numRows())
        throw new Exception("Could not find user");

    $db->nextRecord();

    $acl = new Acl($db->user_id);
    $user = new Acl(USER_ID);
    $this->setTitle($acl->name);

    $db = new DB("friends");
    $db->select("friend_sender = '" . USER_ID . "' AND friend_receiver = '" . $acl->id . "' OR friend_sender = '" . $acl->id . "' AND friend_receiver = '" . USER_ID . "'");

    $friend_show = true;

    if ($db->numRows()) {
        $db->nextRecord();
        if ($db->friend_status == 0)
            $friend_show = false;
        $friends = true;
    } else {
        $friends = false;
    }

    if ($acl->id == USER_ID)
        $friend_show = false;
    ?>
    <div id="profile">
        <h4><?php echo $acl->name ?></h4>
        <?php
        if ($user->Access("x") && $acl->id != USER_ID) {
            ?>
            <a href="<?php echo page("admin", "members", "edit", $acl->name) ?>" style="float:right;"><span class="btn blue"><?php echo _t("Edit profile") ?></span></a>
            <?php
        }
        if ($friend_show) {
            if (!$friends) {
                ?>
                <a href="<?php echo page("profile", "friends", "add", cleanurl($acl->name)) ?>" style="float:right;"><span class="btn"><?php echo _t("Add as friend") ?></span></a>
            <?php } else { ?>
                <a href="<?php echo page("profile", "friends", "remove", cleanurl($acl->name)) ?>" style="float:right;"><span class="btn red"><?php echo _t("Remove as friend") ?></span></a>
                <?php
            }
        }

        $time = time() - 300;
        $online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
        ?>

        <?php
        if ($acl->id != USER_ID) {
            ?>
            <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>" style="float:right;"><span class="btn"><?php echo _t("Private Message") ?></span></a>
            <?php
        }
        ?>
        <table class="profile" cellpadding="5" cellspacing="0" width="100%">
            <tr class="row"><td valign="top" class="avatar" rowspan="14"><img src="<?php echo $acl->Avatar() ?>" id='avatar_image' alt="" style="max-width: 150px;" /></td></tr>
            <tr class="row"><td class="tblhead"><?php echo _t("Last seen") ?></td><td align="left"><?php echo $online ?></td></tr>
            <tr class="row"><td class="tblhead"><?php echo _t("Joined") ?></td><td align="left"><?php echo get_date($acl->added, "", 1); ?></td></tr>
            <tr class="row"><td class="tblhead"><?php echo _t("Group") ?></td><td align="left"><?php echo $acl->group_name ?></td></tr>
            <?php
            if ($user->Access("x")) {
                $dom = @gethostbyaddr($acl->ip);
                $addr = ($dom == $acl->ip || @gethostbyname($dom) != $acl->ip) ? $acl->ip : $acl->ip . ' (' . $dom . ')';
                ?>
                <tr class="row"><td class="tblhead"><?php echo _t("IP address") ?></td><td align="left"><?php echo $addr ?></td></tr>
                <?php
            }

            if (!$acl->anonymous || $user->Access("x")) {
                ?>
                <tr class="row"><td class="tblhead"><?php echo _t("Uploaded") ?></td><td align="left"><?php echo $acl->uploaded() ?></td></tr>
                <tr class="row"><td class="tblhead"><?php echo _t("Downloaded") ?></td><td align="left"><?php echo $acl->downloaded() ?></td></tr>
                <?php
            }
            ?>
            <tr class="row"><td class="tblhead"><?php echo _t("Ratio") ?></td><td align="left"><?php echo $acl->ratio() ?></td></tr>
            <tr class="row"><td class="tblhead"><?php echo _t("Torrents") ?></td><td align="left"><?php echo _t("Seeding") . " " . $acl->seeding(); ?> / <?php echo _t("Leeching") . " " . $acl->leeching(); ?></td></tr>
        </table>
        <?php echo htmlformat($acl->description, true); ?>
    </div>


    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>