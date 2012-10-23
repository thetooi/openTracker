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
include("../../../init.php");

if (!USER_ID)
    die("access denied");

echo "AJAXOK";

switch ($_POST['action']) {

    case 'get':
        ?>
        <ul>
            <?php
            $db = new DB("notifications");
            $db->setColPrefix("notification_");
            $db->setSort("notification_added DESC");
            $db->setLimit("5");
            $db->select("notification_owner = '" . USER_ID . "'");
            if ($db->numRows()) {
                while ($db->nextRecord()) {

                    $user = new Acl($db->user);

                    switch ($db->type) {
                        default:
                            $msg = $db->msg;
                            break;

                        case 'forum_quote':
                            $data = json_decode($db->msg);
                            $forum = new DB("forum_topics");
                            $forum->select("id = '" . $data->topicid . "'");
                            $forum->nextRecord();
                            $msg = _t("has quoted you in") . " <a href='" . page("forums", "action=viewtopic&topicid=" . $data->topicid . "&page=p" . $data->postid . "#post" . $data->postid) . "'>&quot;" . $forum->subject . "&quot;</a>";
                            break;

                        case 'forum_reply':
                            $data = json_decode($db->msg);
                            $forum = new DB("forum_topics");
                            $forum->select("id = '" . $data->topicid . "'");
                            $forum->nextRecord();
                            $msg = _t("has replied in the topic ") . " <a href='" . page("forums", "action=viewtopic&topicid=" . $data->topicid . "&page=p" . $data->postid . "#post" . $data->postid) . "'>&quot;" . $forum->subject . "&quot;</a>";
                            break;
                        case 'shoutbox_highlight':
                            $msg = _t("has highlighted you in the shoutbox");
                            break;
                    }
                    ?>
                    <li>
                        <span class="icon"><img src="<?php echo $user->avatar(); ?>" style="max-height: 40px;"></span>
                        <a href="<?php echo page("profile", "view", $user->name) ?>"><?php echo $user->name ?></a> <?php echo htmlformat($msg) ?><br />
                        <small><?php echo get_date($db->added, '') ?> <?php echo ($db->unread == "0") ? "<font color='red'>" . _t("New!") . "</font>" : ""; ?></small>
                    </li>
                    <?php
                }
            } else {
                ?>
                <li>
                    <?php echo _t("No notifications found"); ?>
                </li>
                <?php
            }
            ?>
            <a href="<?php echo page("notifications"); ?>">
                <li class="archive">
                    <?php echo _t("View More") ?>
                </li></a>
        </ul>
        <?php
        break;


    case 'update':
        $db = new DB("notifications");
        $db->notification_unread = 0;
        $db->update("notification_owner = '" . USER_ID . "'");

        $db = new DB("notifications");
        $db->select("notification_unread = 1 AND notification_owner='" . USER_ID . "'");
        echo $db->numRows();

        break;

    case 'check':
        $db = new DB("notifications");
        $db->select("notification_unread = 1 AND notification_owner='" . USER_ID . "'");
        echo $db->numRows();
        break;
}
?>
