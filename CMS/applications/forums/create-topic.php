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

$this->setTitle("Create Topic");

try {
    $acl = new Acl(USER_ID);

    $forum_id = isset($_GET['forum']) ? 0 + $_GET['forum'] : false;

    if (!$forum_id)
        throw new Exception("missing forum id");

    if (!intval($forum_id))
        throw new Exception("invalid forum id");

    $db = new DB("forum_forums");
    $db->setColPrefix("forum_");
    $db->select("forum_id = '" . $db->escape($forum_id) . "'");

    if (!$db->numRows())
        throw new Exception("forum not found");

    $db->nextRecord();

    if ($db->group > $acl->group)
        throw new Exception("Access denied");

    if (isset($_POST['create'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");
            if (empty($_POST['subject']))
                throw new Exception("Missing subject name");

            if (empty($_POST['content']))
                throw new Exception("Missing topic content");

            $t = new DB("forum_topics");
            $t->setColPrefix("topic_");
            $t->userid = USER_ID;
            $t->subject = $_POST['subject'];
            $t->forum = $forum_id;
            
            if($acl->Access("x")){
                $t->locked = isset($_POST['locked']) ? true : false;
                $t->sticky = isset($_POST['sticky']) ? true : false;
            }
            
            $t->insert();
            $topic_id = $t->getId();
            $topic_name = cleanurl($_POST['subject']);

            $p = new DB("forum_posts");
            $p->setColPrefix("post_");
            $p->topic = $topic_id;
            $p->user = USER_ID;
            $p->content = $_POST['content'];
            $p->added = time();
            $p->insert();
            $post_id = $p->getId();

            $db = new DB("forum_topics");
            $db->topic_lastpost = $post_id;
            $db->update("topic_id = '$topic_id'");

            header("location: " . page("forums", "view-topic", "$topic_name-" . $topic_id, "", "", "page=p$post_id#post$post_id"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    echo "<h4>" . _t("Create topic in") . " " . $db->name . "</h4>";
    ?>

    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <input type="hidden" name="topic_id" value="<?php echo $forum_id ?>" />
            <tr>
                <td><?php echo _t("Subject:") ?></td>
                <td><input type="text" name="subject" value="<?php echo (isset($_POST['subject']) ? $_POST['subject'] : "") ?>" size="50"></td>
            </tr>
            <tr><td></td>
                <td>
                    <?php echo bbeditor("content", 15, 80, (isset($_POST['content']) ? $_POST['content'] : "")); ?>
                </td>
            </tr>
            <?php
            if ($acl->Access("x")) {
                ?>
                <tr>
                    <td>Sticky</td>
                    <td>
                        <label><input name="sticky" value="1" type="radio"> Yes</label>
                        <label><input name="sticky" value="0" type="radio" CHECKED> No</label>
                    </td>
                </tr>
                <tr>
                    <td>Locked</td>
                    <td>
                        <label><input name="locked" value="1" type="radio"> Yes</label>
                        <label><input name="locked" value="0" type="radio" CHECKED> No</label>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr><td></td><td>
                    <input type="submit" name="create" value="<?php echo _t("Create topic"); ?>" /> <input type="submit" name="preview" value="<?php echo _t("Preview"); ?>" />
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['preview'])) {
        $user = new Acl(USER_ID);
        $content = $_POST['content'];
        ?>
        <h4><?php echo _t("Preview"); ?> <?php echo "&quot;" . $_POST['subject'] . "&quot;" ?></h4>
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
        <br />
        <?php
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>