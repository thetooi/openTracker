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

<a href="<?php echo page("profile", "invites", "send") ?>"><span class="btn blue"><?php echo _t("Send invite") ?></span></a>
<br /><br />
<div style="float:left; width: 100%;">
    <?php
    $this->setTitle("Pending invites");

    $db = new DB("users");
    $db->select("user_invited = '" . USER_ID . "' AND user_status = '1'");
    if ($db->numRows()) {
        ?>
        <h4><?php echo _t("Pending invites") ?></h4><br />
        <?php
        while ($db->nextRecord()) {
            $acl = new Acl($db->user_id);
            ?>
            <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
                <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
                <b><?php echo $acl->email ?></b></a>
                <br />
                <b>Uid:</b> <?php echo $acl->id ?><br />
                <b>Key:</b> <?php echo md5($acl->password_secret); ?>
                <br /> <br />
                <a href="<?php echo page("profile", "invites", "delete", "", "", "id=" . $acl->id) ?>" style="float:right;"><span class="btn red">Delete</span></a>
            </div>
            <?php
        }
    }
    ?>
</div>