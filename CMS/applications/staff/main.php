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

$this->setSidebar(true);

$this->setTitle("Staff");
?>
<div style="float:left; width: 100%;">
    <h4><?php echo _t("SysOp") ?></h4>
    <?php
    $db = new DB("users");
    $db->setColPrefix("user_");
    $db->select("user_group = '12'");
    while ($db->nextRecord()) {
        $acl = new Acl($db->id);
        $time = time() - 200;
        $online = ($acl->last_access < $time) ? "Last seen: " . get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%; height: 100px;">
            <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
            <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>)<br /><?php echo $online; ?>
            <br /><br />
            <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>"><span class="btn">PM</span></a> 
            <a href="<?php echo page("profile", "view", $acl->name) ?>"><span class="btn">Profile</span></a>
        </div>
    <?php } ?>
</div>
<div style="float:left; width: 100%; margin-top: 10px; margin-bottom: 10px;">
    <h4><?php echo _t("Administrators") ?></h4>
    <?php
    $db = new DB("users");
    $db->setColPrefix("user_");
    $db->select("user_group = '11'");
    while ($db->nextRecord()) {
        $acl = new Acl($db->id);
        $time = time() - 200;
        $online = ($acl->last_access < $time) ? "Last seen: " . get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
            <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>)<br /><?php echo $online; ?>
            <br /><br />
            <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>"><span class="btn">PM</span></a> 
            <a href="<?php echo page("profile", "view", $acl->name) ?>"><span class="btn">Profile</span></a>
        </div>
    <?php } ?>
</div>
<div style="float:left; width: 100%;">
    <h4><?php echo _t("Moderators") ?></h4>
    <?php
    $db = new DB("users");
    $db->setColPrefix("user_");
    $db->select("user_group = '10'");
    while ($db->nextRecord()) {
        $acl = new Acl($db->id);
        $time = time() - 200;
        $online = ($acl->last_access < $time) ? "Last seen: " . get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
            <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>)<br /><?php echo $online; ?>
            <br /><br />
            <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>"><span class="btn">PM</span></a> 
            <a href="<?php echo page("profile", "view", $acl->name) ?>"><span class="btn">Profile</span></a>
        </div>
    <?php } ?>
</div>