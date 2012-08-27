<div class="col_100" style="margin-bottom: 15px;">
    <div class="col_40">&nbsp;</div>
    <div class="col_60">
        <div style="float:left; margin-right: 15px;">
            <img src="images/forum/forum-default.png" style="float:left; margin-top: -3px; margin-right: 7px;">
            <span style="font-size: 13px; font-weight: bold; ">No New Replies</span>
        </div>
        <div style="float:left; margin-right: 15px;">
            <img src="images/forum/forum-new.png" style="float:left; margin-top: -3px; margin-right: 7px;">
            <span style="font-size: 13px; font-weight: bold; ">New Replies</span>
        </div>
        <div style="float:left; margin-right: 15px;">
            <img src="images/forum/forum-sticky.png" style="float:left; margin-top: -3px; margin-right: 7px;">
            <span style="font-size: 13px; font-weight: bold; ">Sticky Topic</span>
        </div>
        <div style="float:left; margin-right: 15px;">
            <img src="images/forum/forum-closed.png" style="float:left; margin-top: -3px; margin-right: 7px;">
            <span style="font-size: 13px; font-weight: bold; ">Locked Topic</span>
        </div>
    </div>
</div>

<div class="col_100">
    <div class="col_50">
        <h4><?php echo _t("Latest Forum activites") ?></h4>
        <table width="97%" class="forum">
            <?php
            $acl = new Acl(USER_ID);

            $db = new DB("forum_topics as t");
            $db->setCols(array("DISTINCT topic_id", "post_user", "post_added", "topic_id", "topic_subject", "post_id", "topic_lastpost"));
            $db->join("left", "{PREFIX}forum_forums as f", "forum_id", "t.topic_forum");
            $db->join("left", "{PREFIX}forum_posts as p", "t.topic_lastpost", "p.post_id");
            $db->setLimit("5");
            $db->setSort("t.topic_lastpost DESC");
            $db->select("forum_group <= '" . $acl->group . "' GROUP BY topic_id");
            while ($db->nextRecord()) {
                $user = new Acl($db->post_user);
                echo "<tr><td class=''>
        <a href='" . page("profile", "view", $user->name) . "'><strong>" . $user->name . "</strong></a> " . _t("wrote in") . " 
        <a href='" . page("forums", "view-topic", $db->topic_subject . "-" . $db->topic_id, "", "", "page=p" . $db->post_id . "#post" . $db->post_id) . "'>" . $db->topic_subject . "</a>
        <br />" . get_date($db->post_added) . "</td></tr>";
            }
            ?>
        </table>
    </div>
    <div class="col_50">
        <?php
        $db = new DB;

        $db->query("SELECT COUNT(topic_id) as topics FROM {PREFIX}forum_topics");
        $db->nextRecord();

        $topics = $db->topics;

        $db->query("SELECT COUNT(post_id) as posts FROM {PREFIX}forum_posts");
        $db->nextRecord();

        $posts = $db->posts - $topics;


        $db->query("SELECT COUNT(topic_id) as topics_locked FROM {PREFIX}forum_topics WHERE topic_locked = 1");
        $db->nextRecord();

        $topics_locked = $db->topics_locked;

        $db->query("SELECT COUNT(topic_id) as topics_sticky FROM {PREFIX}forum_topics WHERE topic_sticky = 1");
        $db->nextRecord();

        $topics_sticky = $db->topics_sticky;

        $db->query("SELECT post_added FROM {PREFIX}forum_posts ORDER BY post_added ASC LIMIT 1");
        $db->nextRecord();

        $first = $db->post_added;

        $days = dateDiff($first, time());

        $posts_perday = 0;
        if ($days > 0) {
            $posts_perday = round($posts / $days, 2);
        }

        $db->query("SELECT post_added FROM {PREFIX}forum_topics LEFT JOIN {PREFIX}forum_posts ON post_topic = topic_id ORDER BY post_added ASC LIMIT 1");
        $db->nextRecord();

        $first = $db->post_added;

        $days = dateDiff($first, time());

        $topics_perday = 0;
        if ($days > 0) {
            $topics_perday = round($topics / $days, 2);
        }
        ?>

        <h4><?php echo _t("Forum statistics") ?></h4>
        <table width="100%" class="forum" cellspacing="0" cellpadding="10">
            <tr>
                <td align="right">Topics</td>
                <td width="100px"><?php echo $topics; ?></td>
                <td align="right">Replies</td>
                <td width="100px"><?php echo $posts; ?></td>
            </tr>
            <tr>
                <td align="right">Locked topics</td>
                <td width="100px"><?php echo $topics_locked; ?></td>
                <td align="right">Sticky topics</td>
                <td width="100px"><?php echo $topics_sticky; ?></td>
            </tr>
            <tr>
                <td align="right">Topics per day</td>
                <td width="100px"><?php echo $topics_perday; ?></td>
                <td align="right">Posts per day</td>
                <td width="100px"><?php echo $posts_perday; ?></td>
            </tr>
        </table>
    </div>
</div>