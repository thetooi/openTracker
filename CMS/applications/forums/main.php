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

$this->setTitle("Forum");

$acl = new Acl(USER_ID);

/**
 * Return the last post
 * @param int $id
 * @return array 
 */
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
    <div class="col_100">
        <h4><?php echo $forum_cat->title ?></h4>
        <table width="100%" cellpadding="10" cellspacing="0" class="forum">
            <thead>
                <tr>
                    <td width="46px" class=""></td>
                    <td width="55%" class=""><?php echo _t("Forum name"); ?></td>
                    <td width="126px" class="" align="right"><?php echo _t("Stats"); ?></td>
                    <td class=""><?php echo _t("Last post"); ?></td>
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

                    $db = new DB("forum_posts");
                    $db->setCol("COUNT(post_id) as posts");
                    $db->join("left", "{PREFIX}forum_topics", "topic_id", "post_topic");
                    $db->join("left", "{PREFIX}forum_forums", "forum_id", "topic_forum");
                    $db->select("forum_id = '" . $forums->id . "'");
                    $db->nextRecord();

                    $posts = $db->posts - $topics;

                    $db = new DB("forum_topics");
                    $db->join("left", "{PREFIX}forum_posts", "topic_id", "post_topic");
                    $db->setSort("post_added DESC");
                    $db->setLimit("1");
                    $db->select("topic_forum = '" . $forums->id . "'");
                    $db->nextRecord();

                    if (!$db->numRows())
                        $last_post = "--";
                    else {
                        $user = new Acl($db->post_user);
                        $last_post = _t("By") . " <a href='" . page("profile", "view", strtolower($user->name)) . "'>" . $user->name . "</a> in <a href='" . page("forums", "view-topic", $db->topic_subject . "-" . $db->topic_id) . "'>" . $db->topic_subject . "</a><br />" . get_date($db->post_added);
                    }

                    $r = new DB("forum_postread");
                    $r->setColPrefix("post_");
                    $r->select("post_userid = '" . USER_ID . "' AND post_topicid = '" . $db->topic_id . "'");
                    if ($r->numRows()) {
                        $r->nextRecord();
                        $last = $r->lastpostread;
                    } else {
                        $last = 0;
                    }
                    $new = ($db->post_id > $last) ? true : false;
                    $image = ($new ? "forum-new.png" : "forum-default.png");
                    ?>
                    <tr>
                        <td class="" align="center"><img src="images/forum/<?php echo $image; ?>"></td>
                        <td class=""><a href="<?php echo page("forums", "view-forum", cleanurl($forums->name) . "-" . $forums->id) ?>" class="topic-title"><?php echo $forums->name ?></a><?php echo $forums->description != "" ? "<br /><span class='description'>" . $forums->description . "</span>" : "" ?></td>
                        <td class="stats" align="right"><?php echo $topics; ?> <?php echo _t("Topics") ?><br /><?php echo $posts ?> <?php echo _t("Replies") ?></td>
                        <td class=""><?php echo $last_post; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
}

$tpl = new Template(PATH_APPLICATIONS . "forums/tpl/");
$tpl->build("bottom-info.php");
?>
