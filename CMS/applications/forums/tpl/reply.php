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

    $db = new DB("forum_topics");
    $db->select("topic_id = '" . $db->escape($this->topic_id) . "'");

    if (!$db->numRows())
        throw new Exception("topic not found");

    $db->nextRecord();

    if ($db->topic_locked == "1")
        throw new Exception("Topic is locked");

    if (isset($_POST['reply'])) {
        try {

            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");

            if (!isset($_POST['topic_id']))
                throw new Exception("missing topic id");

            if (!intval($_POST['topic_id']))
                throw new Exception("invalid topic id");

            if (!isset($_POST['content']))
                throw new Exception("missing content");

            if (empty($_POST['content']))
                throw new Exception("Cannot reply with an empty message");

            $db = new DB("forum_topics");
            $db->select("topic_id = '" . $db->escape($_POST['topic_id']) . "'");

            if (!$db->numRows())
                throw new Exception("topic not found");

            $db->nextRecord();

            if ($db->topic_locked == "1")
                throw new Exception("Topic is locked");

            $topic_name = strtolower(cleanurl($db->topic_subject));

            $db = new DB("forum_posts");
            $db->setColPrefix("post_");
            $db->topic = $_POST['topic_id'];
            $db->user = USER_ID;
            $db->content = $_POST['content'];
            $db->added = time();
            $db->insert();
            $id = $db->getId();

            $db = new DB("forum_topics");
            $db->topic_lastpost = $id;
            $db->update("topic_id = '".$db->escape($_POST['topic_id'])."'");

            header("location: " . page("forums", "view-topic", "$topic_name-" . $_POST['topic_id'], "", "", "page=p$id#post$id"));
        } Catch (Exception $e) {
            echo Error(_t($e->getMessage()));
        }
    }

    if (isset($_POST['preview'])) {
        $user = new Acl(USER_ID);
        $content = $_POST['content'];
        ?>
        <h4><?php echo _t("Preview"); ?></h4>
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
                        <small>Posted at <?php echo get_date(time(), "", 0, 0) ?></small><br />
        <?php
        echo htmlformat($content, true);
        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="border-right border-bottom">
                        <a href="<?php echo page("profile", "view", strtolower($user->name)); ?>"><span class="btn">Profile</span></a>
                        <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $user->id); ?>"><span class="btn">PM</span></a>
                    </td>
                    <td class="border-bottom" align="right">
                    </td>
                </tr>
            </tbody>
        </table>
        <br /><br /><br />
        <?php
    }
    ?>
    <table align="center">
        <tr>
            <td>
                <form method="post">
                    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
                    <input type="hidden" name="topic_id" value="<?php echo $this->topic_id ?>" />
    <?php echo bbeditor("content", 10, 80, (isset($_POST['content']) ? $_POST['content'] : "")); ?>
                    <br />
                    <input type="submit" name="reply" value="<?php echo _t("Reply"); ?>" /> <input type="submit" name="preview" value="<?php echo _t("Preview"); ?>" />
                </form>
            </td>
        </tr>
    </table>
    <?php
} Catch (Exception $e) {
    echo Error(_t($e->getMessage()));
}
?>