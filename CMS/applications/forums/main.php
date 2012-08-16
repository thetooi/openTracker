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

$tpl = new Template(PATH_APPLICATIONS . "forums/tpl/");
$tpl->build("search.php");
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
                    <td class="border-bottom" align="center"><img src="images/forum/<?php echo $image; ?>"></td>
                    <td class="border-bottom border-right"><a href="<?php echo page("forums", "view-forum", cleanurl($forums->name) . "-" . $forums->id) ?>"><?php echo $forums->name ?></a><?php echo $forums->description != "" ? "<br />" . $forums->description : "" ?></td>
                    <td class="border-bottom border-right" align="center"><?php echo $topics; ?></td>
                    <td class="border-bottom"><?php echo $last_post; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


<?php } ?>
