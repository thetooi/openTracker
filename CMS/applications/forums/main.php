<?php
$this->setTitle("Forum");

$acl = new Acl(USER_ID);

function getLastPost($id) {

    $db = new DB("forum_posts");
    $db->select("post_id = '" . $id . "'");
    if (!$db->numRows())
        return false;

    $db->nextRecord();
    return $db->record;
}

$forum_cat = new DB("forum_categories");
$forum_cat->setColPrefix("category_");
$forum_cat->setSort("category_sort ASC");
$forum_cat->select("category_group <= " . $acl->group . "");
while ($forum_cat->nextRecord()) {
    ?>
    <h4><?php echo $forum_cat->title ?></h4>
    <table width="100%" cellpadding="5" cellspacing="0" class="forum">
        <thead>
            <tr>
                <td width="46px" class="border-bottom"></td>
                <td width="60%" class="border-bottom border-right"><?php echo _t("Forum name"); ?></td>
                <td width="66px" class="border-bottom border-right" align="center"><?php echo _t("Topics"); ?></td>
                <td class="border-bottom"><?php echo _t("Last post"); ?></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $forums = new DB("forum_forums");
            $forums->setColPrefix("forum_");
            $forums->setSort("forum_sort ASC");
            $forums->select("forum_group <= " . $acl->group . " AND forum_category = '" . $forum_cat->id . "'");
            while ($forums->nextRecord()) {

                $db = new DB;

                $db->query("SELECT COUNT(topic_id) as topics FROM {PREFIX}forum_topics WHERE topic_forum = '" . $forums->id . "'");
                $db->nextRecord();
                $topics = $db->topics;

                $db = new DB("forum_topics");
                $db->join("left", "{PREFIX}forum_posts", "topic_id", "post_topic");
                $db->setSort("post_added DESC");
                $db->setLimit("1");
                $db->select("topic_forum = '" . $forums->id . "'");

                if (!$db->numRows())
                    $last_post = "--";
                else {
                    $db->nextRecord();
                    $user = new Acl($db->post_user);
                    $last_post = _t("By") . " <a href='" . page("profile", "view", strtolower($user->name)) . "'>" . $user->name . "</a> in <a href='" . page("forums", "view-topic", $db->topic_subject . "-" . $db->topic_id) . "'>" . $db->topic_subject . "</a><br />" . get_date($db->post_added);
                }
                ?>
                <tr>
                    <td class="border-bottom" align="center"><img src="images/forum/forum-default.png"></td>
                    <td class="border-bottom border-right"><a href="<?php echo page("forums", "view-forum", cleanurl($forums->name) . "-" . $forums->id) ?>"><?php echo $forums->name ?></a><?php echo $forums->description != "" ? "<br />" . $forums->description : "" ?></td>
                    <td class="border-bottom border-right" align="center"><?php echo $topics; ?></td>
                    <td class="border-bottom"><?php echo $last_post; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


<?php } ?>
