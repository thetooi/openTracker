<div id="sortables">
    <?php
    $acl = new Acl(USER_ID);

    $forum_cat = new DB("forum_categories");
    $forum_cat->setColPrefix("category_");
    $forum_cat->setSort("category_sort ASC");
    $forum_cat->select("category_group <= " . $acl->group . "");
    while ($forum_cat->nextRecord()) {
        ?>
        <div class="move_category" style="float:left; width: 100%;" id="cat_<?php echo $forum_cat->id ?>">
            <h4><?php echo $forum_cat->title ?></h4>
            <a href="<?php echo page("admin", "forum", "edit-category", "", "", "id=" . $forum_cat->id); ?>"><img src="images/icons/edit_16.png" class="rel" title="Edit forum category" /></a>
            <a href="<?php echo page("admin", "forum", "delete-category", "", "", "id=" . $forum_cat->id . "&confirm"); ?>"><img src="images/icons/trash_16.png" class="rel" title="Delete forum category" /></a>
            <img src="images/icons/move_16.png" class="rel move_cat"  style="cursor: move;" title="Move forum category" />
            <table id="forum" width="100%" cellpadding="5" cellspacing="0" class="forum">
                <thead>
                    <tr>
                        <td width="40px" class="border-bottom"></td>
                        <td class="border-bottom"><?php echo _t("Forum name"); ?></td>
                        <td width="70px" class="border-bottom border-right"></td>
                        <td width="66px" class="border-bottom border-right" align="center"><?php echo _t("Topics"); ?></td>
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
                            $last_post = _t("By") . " <a href='" . page("profile", "view", strtolower($user->name)) . "'>" . $user->name . "</a> in " . $db->topic_subject . "<br />" . get_date($db->post_added);
                        }
                        ?>
                        <tr id="forum_<?php echo $forums->id ?>">
                            <td class="border-bottom" align="center"><img src="images/forum/forum-default.png"></td>
                            <td class="border-bottom"><a href="<?php echo page("forums", "view-forum", cleanurl($forums->name) . "-" . $forums->id) ?>"><?php echo $forums->name ?></a></td>
                            <td class="border-bottom border-right">
                                <a href="<?php echo page("admin", "forum", "edit-forum", "", "", "id=" . $forums->id); ?>"><img src="images/icons/edit_16.png" class="rel" title="Edit forum" /></a>
                                <a href="<?php echo page("admin", "forum", "delete-forum", "", "", "id=" . $forums->id . "&confirm"); ?>"><img src="images/icons/trash_16.png" class="rel" title="Delete forum" /></a>
                                <img src="images/icons/move_16.png" class="rel move_forum" style="cursor: move;" title="Move forum" />
                            </td>
                            <td class="border-bottom" align="center"><?php echo $topics; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>