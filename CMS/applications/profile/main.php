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

<script type="text/javascript">
    $(".show").live("click", function(){
        var id = $(this).attr("rel");
        if($(id).is(":visible")){
            $(id).hide();
        }else{
            $(id).show();
        }
    });
</script>
<?php
$acl = new Acl(USER_ID);
$this->setTitle("Profile");
$time = time() - 300;
$online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
?>
<div id="profile">
    <h4><?php echo $acl->name ?></h4>
    <a href="<?php echo page("profile", "edit") ?>" style="float:right;"><span class="btn"><?php echo _t("Edit profile") ?></span></a>
    <table class="profile" cellpadding="5" cellspacing="0">
        <tr class="row"><td valign="top" class="avatar" rowspan="14"><img src="<?php echo $acl->Avatar() ?>" id='avatar_image' alt="" style="max-width: 150px;" /></td></tr>
        <tr class="row"><td class="tblhead"><?php echo _t("Last seen") ?></td><td align="left"><?php echo $online ?></td></tr>
        <tr class="row"><td class="tblhead"><?php echo _t("Joined") ?></td><td align="left"><?php echo get_date($acl->added, "date"); ?></td></tr>
        <tr class="row"><td class="tblhead"><?php echo _t("Uploaded") ?></td><td align="left"><?php echo $acl->uploaded() ?></td></tr>
        <tr class="row"><td class="tblhead"><?php echo _t("Downloaded") ?></td><td align="left"><?php echo $acl->downloaded() ?></td></tr>
        <tr class="row"><td class="tblhead"><?php echo _t("Ratio") ?></td><td align="left"><?php echo $acl->ratio() ?></td></tr>
        <?php
        if ($acl->invited != "") {
            $user = new Acl($acl->invited);
            ?>
            <tr class="row"><td class="tblhead"><?php echo _t("Invited by") ?></td><td align="left"><a href="<?php echo page("profile", "view", $user->name) ?>"><?php echo $user->name ?></a></td></tr>
            <?php
        }
        ?>
        <tr class="row"><td class="tblhead"><?php echo _t("Invites") ?><br />
                <a href="<?php echo page("profile", "invites") ?>">[<?php echo _t("My invites") ?>]</a>
            </td><td align="left"><?php echo $acl->invites() ?></td></tr>
        <tr class="row"><td class="tblhead"><?php echo _t("Torrents") ?></td><td align="left"><a style="cursor: pointer;" class="show" rel="#seeding"><?php echo _t("Seeding") . " " . $acl->seeding(); ?></a> / <a style="cursor: pointer;" class="show" rel="#leeching"><?php echo _t("Leeching") . " " . $acl->leeching(); ?></a></td></tr>
    </table>
</div>

<div id="seeding" style="display:none;">
    <h4>Seeding</h4>
    <table id="browse" class="forum" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <td width="40px" class="border-bottom">
                </td>
                <td width="50%" class="border-bottom border-right">
                    <?php echo _t("Name"); ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo _t("Uploaded"); ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo _t("Downloaded"); ?>
                </td>
                <td class="border-bottom" align="center">
                    <?php echo _t("Seeded for"); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("peers");
            $db->join("left", "{PREFIX}torrents", "torrent_id", "peer_torrent");
            $db->join("left", "{PREFIX}categories", "category_id", "torrent_category");
            $db->select("peer_seeder = '1' AND peer_userid  = '" . USER_ID . "'");
            while ($db->nextRecord()) {
                ?>
                <tr>
                    <td width="40px" class="border-bottom">
                        <img src="images/categories/<?php echo $db->category_icon ?>" />
                    </td>
                    <td width="50%" class="border-bottom border-right">
                        <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $db->torrent_id) ?>"><?php echo $db->torrent_name ?></a>
                    </td>
                    <td class="border-right border-bottom" align="center">
                        <?php echo bytes($db->peer_uploaded) ?>
                    </td>
                    <td class="border-right border-bottom" align="center">
                        <?php echo bytes($db->peer_downloaded) ?>
                    </td>
                    <td class="border-bottom" align="center">
                        <?php echo timediff($db->peer_started, time()); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<div id="leeching" style="display:none;">
    <h4>Leeching</h4>
    <table id="browse" class="forum" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <td width="40px" class="border-bottom">
                </td>
                <td width="50%" class="border-bottom border-right">
                    <?php echo _t("Name"); ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo _t("Uploaded"); ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo _t("Downloaded"); ?>
                </td>
                <td class="border-bottom" align="center">
                    <?php echo _t("Seeded for"); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("peers");
            $db->join("left", "{PREFIX}torrents", "torrent_id", "peer_torrent");
            $db->join("left", "{PREFIX}categories", "category_id", "torrent_category");
            $db->select("peer_seeder = '0' AND peer_userid  = '" . USER_ID . "'");
            while ($db->nextRecord()) {
                ?>
                <tr>
                    <td width="40px" class="border-bottom">
                        <img src="images/categories/<?php echo $db->category_icon ?>" />
                    </td>
                    <td width="50%" class="border-bottom border-right">
                        <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $db->torrent_id) ?>"><?php echo $db->torrent_name ?></a>
                    </td>
                    <td class="border-right border-bottom" align="center">
                        <?php echo bytes($db->peer_uploaded) ?>
                    </td>
                    <td class="border-right border-bottom" align="center">
                        <?php echo bytes($db->peer_downloaded) ?>
                    </td>
                    <td class="border-bottom" align="center">
                        <?php echo timediff($db->peer_started, time()); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>