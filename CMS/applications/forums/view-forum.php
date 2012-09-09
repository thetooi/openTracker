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
    $acl = new Acl(USER_ID);

    $arg = $this->args['var_a'];
    $id = end(explode("-", $this->args['var_a']));

    if (!intval($id))
        throw new Exception("Missing forum id");

    $db = new DB("forum_forums");
    $db->join("left", "{PREFIX}forum_categories", "category_id", "forum_category");
    $db->select("forum_id = '" . $db->escape($id) . "'");
    $db->nextRecord();

    if ($db->forum_group > $acl->group)
        throw new Exception("Access denied");

    echo "<h4>" . $db->forum_name . "</h4>";
    echo "<a href='".page("forums")."'>" . $db->category_title . "</a> > <a href='".page("forums", "view-forum", $this->args['var_a'])."'>" . $db->forum_name."</a>";
    $this->setTitle($db->forum_name);


    $db = new DB("forum_topics");
    $db->setColPrefix("topic_");
    $db->join("left", "{PREFIX}forum_posts", "post_topic", "topic_id");
    $db->setSort("topic_sticky DESC, post_added DESC");
    $db->select("topic_forum = '" . $db->escape($id) . "' GROUP BY post_topic");

    $perpage = 40;
    $pager = new Pager;
    $pager->perpage = $perpage;
    $pager->count = $db->numRows();
    $pager->href = array("forums", "view-forum", $arg);
    $pager->build();
    echo $pager->pager_top;
    ?>
    <br />
    <a href="<?php echo page("forums", "create-topic", "", "", "", "forum=" . $id); ?>" style="float:right;"><span class="btn"><?php echo _t("Create new topic"); ?></span></a>
    <br /><br />
    <table width="100%" cellpadding="5" cellspacing="0" class="forum">
        <thead>
            <tr>
                <td width="46px" class=""></td>
                <td class=""><?php echo _t("Topic"); ?></td>
                <td class="" align="center"><?php echo _t("Started by"); ?></td>
                <td class="" align="right"><?php echo _t("Stats"); ?></td>
                <td class=""><?php echo _t("Last post info"); ?></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("forum_topics");
            $db->setColPrefix("topic_");
            $db->join("left", "{PREFIX}forum_posts", "post_topic", "topic_id");
            $db->setSort("topic_sticky DESC, post_added DESC");
            $db->setLimit($pager->limit);
            $db->select("topic_forum = '" . $db->escape($id) . "' GROUP BY post_topic");
            while ($db->nextRecord()) {

                $user = new Acl($db->userid);

                $q = new DB;
                $q->query("SELECT COUNT(post_id) as posts FROM {PREFIX}forum_posts WHERE post_topic = '" . $db->id . "'");
                $q->nextRecord();
                $posts = $q->posts - 1;

                $q = new DB("forum_topics");
                $q->join("left", "{PREFIX}forum_posts", "topic_id", "post_topic");
                $q->setSort("post_added DESC");
                $q->setLimit("1");
                $q->select("topic_id= '" . $db->id . "'");

                $r = new DB("forum_postread");
                $r->setColPrefix("post_");
                $r->select("post_userid = '" . USER_ID . "' AND post_topicid = " . $db->id);
                if ($r->numRows()) {
                    $r->nextRecord();
                    $last = $r->lastpostread;
                } else {
                    $last = 0;
                }
                $q->nextRecord();
                $new = ($q->post_id > $last) ? true : false;

                if (!$db->numRows())
                    $last_post = "--";
                else {
                    $user = new Acl($q->post_user);
                    $last_post = get_date($q->post_added) . "<br />" . _t("By") . ": <a href='" . page("profile", "view", strtolower($user->name)) . "'>" . $user->name . "</a>";
                }

                $image = "forum-default.png";

                if ($db->sticky)
                    $image = "forum-sticky.png";
                if ($db->locked)
                    $image = "forum-closed.png";

                $image = ($new ? "forum-new.png" : $image);
                ?>
                <tr>
                    <td width="46px" class="" align="center"><img src="images/forum/<?php echo $image; ?>"></td>
                    <td class=""><a href="<?php echo page("forums", "view-topic", cleanurl($db->subject) . "-" . $db->id); ?>" class="topic-title"><?php echo $db->subject ?></a></td>
                    <td width="150px" class="" align="center"><?php echo $user->name ?></td>
                    <td width="120px" class="stats" align="right"><?php echo $posts ?> Replies</td>
                    <td width="180px"class=""><?php echo $last_post ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <?php echo $pager->pager_bottom; ?>

    <?php
    $tpl = new Template(PATH_APPLICATIONS . "forums/tpl/");
    $tpl->build("bottom-info.php");
} Catch (Exception $e) {
    echo Error(_t($e->getMessage()));
}
?>
