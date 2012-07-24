<?php
try {
    $acl = new Acl(USER_ID);

    $id = end(explode("-", $this->args['var_a']));

    if (!intval($id))
        throw new Exception("Missing forum id");

    $db = new DB("forum_forums");
    $db->select("forum_id = '" . $db->escape($id) . "'");
    $db->nextRecord();

    if ($db->forum_group > $acl->group)
        throw new Exception("Access denied");

    echo "<h4>" . $db->forum_name . "</h4>";

    $this->setTitle($db->forum_name);
    ?>

    <a href="<?php echo page("forums", "create-topic", "", "", "", "forum=" . $id); ?>" style="float:right;"><span class="btn"><?php echo _t("Create new topic"); ?></span></a>
    <br /><br />
    <table width="100%" cellpadding="5" cellspacing="0" class="forum">
        <thead>
            <tr>
                <td width="46px" class="border-bottom"></td>
                <td width="60%" class="border-bottom border-right"><?php echo _t("Topic"); ?></td>
                <td width="66px" class="border-bottom border-right" align="center"><?php echo _t("Replies"); ?></td>
                <td class="border-bottom"><?php echo _t("Last post"); ?></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("forum_topics");
            $db->setColPrefix("topic_");
            $db->join("left", "{PREFIX}forum_posts", "post_topic", "topic_id");
            $db->setSort("topic_sticky DESC, post_added DESC");
            $db->select("topic_forum = '" . $db->escape($id) . "' GROUP BY post_topic");
            while ($db->nextRecord()) {

                $q = new DB;
                $q->query("SELECT COUNT(post_id) as posts FROM {PREFIX}forum_posts WHERE post_topic = '" . $db->id . "'");
                $q->nextRecord();
                $posts = $q->posts - 1;

                $q = new DB("forum_topics");
                $q->join("left", "{PREFIX}forum_posts", "topic_id", "post_topic");
                $q->setSort("post_added DESC");
                $q->setLimit("1");
                $q->select("topic_id= '" . $db->id . "'");

                if (!$db->numRows())
                    $last_post = "--";
                else {
                    $q->nextRecord();
                    $user = new Acl($q->post_user);
                    $last_post = _t("By") . " <a href='" . page("profile", "view", strtolower($user->name)) . "'>" . $user->name . "</a> in 
                        <a href='" . page("forums", "view-topic", $q->topic_subject . "-" . $q->topic_id) . "'>" . $q->topic_subject . "</a>
                            <br />" . get_date($q->post_added);
                }

                $image = "forum-default.png";

                if ($db->sticky)
                    $image = "forum-sticky.png";
                if ($db->locked)
                    $image = "forum-closed.png";
                ?>
                <tr>
                    <td width="46px" class="border-bottom" align="center"><img src="images/forum/<?php echo $image; ?>"></td>
                    <td width="60%" class="border-bottom border-right"><a href="<?php echo page("forums", "view-topic", cleanurl($db->subject) . "-" . $db->id); ?>"><?php echo $db->subject ?></a></td>
                    <td width="66px" class="border-bottom border-right" align="center"><?php echo $posts ?></td>
                    <td class="border-bottom"><?php echo $last_post ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <a href="<?php echo page("forums", "create-topic", "", "", "", "forum=" . $id); ?>" style="float:right; margin-top: 10px;"><span class="btn"><?php echo _t("Create new topic"); ?></span></a>

    <?php
} Catch (Exception $e) {
    echo Error(_t($e->getMessage()));
}
?>
