<?php
$this->setTitle("Quote Post");

try {

    $acl = new Acl(USER_ID);

    if (!isset($_GET['post']))
        throw new Exception("missing post");

    if (!intval($_GET['post']))
        throw new Exception("invalid id");

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

            if ($db->locked)
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

            header("location: " . page("forums", "view-topic", "$topic_name-" . $_POST['topic_id'], "", "", "page=p$id#post$id"));
        } Catch (Exception $e) {
            echo Error(_t($e->getMessage()));
        }
    }

    $db = new DB("forum_posts");
    $db->join("left", "{PREFIX}forum_topics", "topic_id", "post_topic");
    $db->select("post_id = '" . $db->escape($_GET['post']) . "'");
    $db->nextRecord();

    if ($db->topic_locked == "1")
        throw new Exception("Topic is locked");

    $user = new Acl($db->post_user);

    $allowed = false;
    ?>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <?php echo bbcode_buttons("editor"); ?><br />
        <input type="hidden" name="topic_id" value="<?php echo $db->post_topic ?>" />
        <textarea cols="80" id="editor" name="content" rows="15"><?php echo isset($_POST['content']) ? $_POST['content'] : "[quote=" . $user->name . "]" . $db->post_content . "[/quote]"; ?></textarea><br />
        <input type="submit" name="reply" value="<?php echo _t("Quote"); ?>" /> <input type="submit" name="preview" value="<?php echo _t("Preview"); ?>" />
    </form>
    <?php
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
        <br />
        <?php
    }
} Catch (Exception $e) {
    echo error($e->getMessage());
}
?>
