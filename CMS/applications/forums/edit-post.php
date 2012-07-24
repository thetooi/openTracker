<?php
$this->setTitle("Edit Post");

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
            $db->content = $_POST['content'];
            $db->edited_by = USER_ID;
            $db->edited_date = time();
            $db->update("post_id = '" . $db->escape($_GET['post']) . "'");
            $id = $_GET['post'];

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

    $allowed = false;

    if ($acl->id == $db->post_user)
        $allowed = true;

    if ($acl->Access("x"))
        $allowed = true;

    if (!$allowed)
        throw new Exception("Not owner, access denied");
    ?>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <input type="hidden" name="topic_id" value="<?php echo $db->post_topic ?>" />
        <?php echo bbeditor("content", 15, 80, $db->post_content); ?>
        <br />
        <input type="submit" name="reply" value="<?php echo _t("Save"); ?>" />
    </form>

    <?php
} Catch (Exception $e) {
    echo error($e->getMessage());
}
?>
