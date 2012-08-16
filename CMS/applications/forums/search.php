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

    if (!isset($_POST['q']))
        throw new Exception("Missing search string");

    if (empty($_POST['q']))
        throw new Exception("Missing search string");
    
    if(strlen($_POST['q']) < 4)
        throw new Exception("Your search string has to contain atleast 4 characters");

    $db = new DB("forum_posts");
    $db->join("left", "{PREFIX}forum_topics", "topic_id", "post_topic");
    $db->select("post_content LIKE('%" . $db->escape($_POST['q']) . "%')");
    if (!$db->numRows())
        throw new Exception("No forum posts found");
    while ($db->nextRecord()) {
        $user = new Acl($db->post_user);
        ?>
        <h4><a href="<?php echo page("forums", "view-topic", cleanurl($db->topic_subject) . "-" . $db->topic_id, "", "", "page=p" . $db->post_id . "#post" . $db->post_id) ?>"><?php echo $db->topic_subject; ?></a></h4>
        <table class="forum" id="post<?php echo $db->post_id ?>" width="100%" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <td width="150px" class="border-bottom"><a href="<?php echo page("profile", "view", strtolower($user->name)); ?>"><?php echo $user->name; ?></a></td>
                    <td class="border-bottom"></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td valign="top" class="border-right"><img src="<?php echo $user->avatar(); ?>" width="150px"/></td>
                    <td valign="top">
                        <small><?php echo _t("Posted ") ?> <?php echo get_date($db->post_added, "", 0, 0) ?></small><br />
                        <?php
                        echo htmlformat($db->post_content, true);

                        if ($db->post_edited_by != 0) {
                            $edited = new Acl($db->post_edited_by);
                            echo "<br /><br /><small>" . _t("Last edited") . " " . get_date($db->post_edited_date) . " " . _t("by") . " <a href='" . page("profile", "view", cleanurl($edited->name)) . "'>" . $edited->name . "</a></small>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="border-right border-bottom">
                        <a href="<?php echo page("profile", "view", strtolower($user->name)); ?>"><span class="btn"><?php echo _t("Profile") ?></span></a>
                        <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $user->id); ?>"><span class="btn"><?php echo _t("PM") ?></span></a>
                    </td>
                    <td class="border-bottom" align="right">
                    </td>
                </tr>
            </tbody>
        </table>
        <br />
        <?php
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
