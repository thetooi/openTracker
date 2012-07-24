<h4><?php echo _t("Forum activites") ?></h4>
<table width="100%">
    <?php
    $acl = new Acl(USER_ID);

    $db = new DB("forum_posts");
    $db->setCols(array("DISTINCT topic_id", "post_user", "post_added", "topic_id", "topic_subject", "post_id"));
    $db->join("left", "{PREFIX}forum_topics", "topic_id", "post_topic");
    $db->join("left", "{PREFIX}forum_forums", "forum_id", "topic_forum");
    $db->setLimit("5");
    $db->setSort("post_id DESC");
    $db->select("forum_group <= '" . $acl->group . "' GROUP BY topic_id");
    while ($db->nextRecord()) {
        $user = new Acl($db->post_user);
        echo "<tr><td class='border-bottom'>
        <a href='" . page("profile", "view", $user->name) . "'><strong>" . $user->name . "</strong></a> " . _t("wrote in") . " 
        <a href='" . page("forums", "view-topic", $db->topic_subject . "-" . $db->topic_id, "", "", "page=p" . $db->post_id . "#post" . $db->post_id) . "'>" . $db->topic_subject . "</a>
        <br />" . get_date($db->post_added) . "</td></tr>";
    }
    ?>
</table>