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

if(!defined("INCLUDED"))
    die("Access denied");

$this->setTitle("View Topic");

try {
    $acl = new Acl(USER_ID);

    if (!isset($this->args['var_a']) && empty($this->args['var_a']))
        throw new Exception("Missing variable");

    $id = end(explode("-", $this->args['var_a']));

    if (!intval($id))
        throw new Exception("Missing forum id");

    $page = isset($_GET["page"]) ? $_GET["page"] : false;


    if ($acl->Access("x")) {
        $db = new DB("forum_topics");
        $db->select("topic_id = '" . $db->escape($id) . "'");
        $db->nextRecord();
        if (isset($_POST['lock'])) {
            if ($db->topic_locked == "1") {
                $db->query("UPDATE {PREFIX}forum_topics SET topic_locked = '0' WHERE topic_id = '" . $db->escape($id) . "'");
            } else {
                $db->query("UPDATE {PREFIX}forum_topics SET topic_locked = '1' WHERE topic_id = '" . $db->escape($id) . "'");
            }
        } else if (isset($_POST['sticky'])) {
            if ($db->topic_sticky == "1") {
                $db->query("UPDATE {PREFIX}forum_topics SET topic_sticky = '0' WHERE topic_id = '" . $db->escape($id) . "'");
            } else {
                $db->query("UPDATE {PREFIX}forum_topics SET topic_sticky = '1' WHERE topic_id = '" . $db->escape($id) . "'");
            }
        }
    }

    $db = new DB("forum_topics");
    $db->select("topic_id = '" . $db->escape($id) . "'");
    $db->nextRecord();
    $forum_id = $db->topic_forum;

    echo "<h4>" . $db->topic_subject . "</h4>";

    if ($acl->Access("x")) {
        echo "
        <form method='post' style='float:right;'>
            <input type='submit' name='lock' value='" . ($db->topic_locked == "1" ? _t("Unlock Topic") : _t("Lock Topic")) . "'>
            <input type='submit' name='sticky' value='" . ($db->topic_sticky == "1" ? _t("Unsticky Topic") : _t("Sticky Topic")) . "'>
        </form>
        ";
    }

    $this->setTitle($db->topic_subject);

    $db = new DB("forum_forums");
    $db->select("forum_id = '" . $forum_id . "'");
    $db->nextRecord();

    if ($db->forum_group > $acl->group)
        throw new Exception("Access denied");

    $db = new DB("forum_posts");
    $db->setSort("post_added ASC");
    $db->select("post_topic = '" . $db->escape($id) . "'");
    $perpage = ($acl->posts_perpage != 0) ? $acl->posts_perpage : 10;
    $pagemenu = "<p>\n";
    $pages = ceil($db->numRows() / $perpage);

    if ($page[0] == "p") {
        $findpost = substr($page, 1);
        $res = new DB;
        $res->query("SELECT post_id FROM {PREFIX}forum_posts WHERE post_topic = '" . $id . "' ORDER BY post_added ASC");
        $i = 1;
        while ($res->nextRecord()) {
            if ($res->post_id == $findpost)
                break;
            ++$i;
        }
        $page = ceil($i / $perpage);
    }

    if ($page == "last")
        $page = $pages;
    else {
        if ($page < 1)
            $page = 1;
        elseif ($page > $pages)
            $page = $pages;
    }

    $offset = $page * $perpage - $perpage;

    for ($i = 1; $i <= $pages; ++$i) {
        if ($i == $page)
            $pagemenu .= "<font class='gray'>$i</font>\n";

        else
            $pagemenu .= "<a href='" . page("forums", "view-topic", $this->args['var_a'], "", "", "page=$i") . "'>$i</a>\n";
    }

    if ($page == 1)
        $pagemenu .= "<br /><font class='gray'>" . _t("Prev") . "</font>";
    else
        $pagemenu .= "<br /><a href='" . page("forums", "view-topic", $this->args['var_a'], "", "", "page=" . ($page - 1)) . "'>" . _t("Prev") . "</a>";

    $pagemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($page == $pages)
        $pagemenu .= "<font class='gray'>" . _t("Next") . "</font></p>\n";

    else
        $pagemenu .= "<a href='" . page("forums", "view-topic", $this->args['var_a'], "", "", "page=" . ($page + 1)) . "'>" . _t("Next") . "</a></p>\n";
    $db->setLimit("$offset,$perpage");
    $db->select("post_topic = " . $db->escape($id));
    $i = 1;

    echo $pagemenu;

    while ($db->nextRecord()) {
        $user = new Acl($db->post_user);

        $edit = false;

        if ($acl->id == $user->id)
            $edit = true;

        if ($acl->Access("x"))
            $edit = true;

        $time = time() - 300;
        $online = ($user->last_access < $time) ? _t("Online") . " " . get_date($user->last_access) : "<b><font color='green'>Online</font></b>";
        ?>
        <table class="forum" id="post<?php echo $db->post_id ?>" width="100%" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <td width="150px" class="border-bottom"><a href="<?php echo page("profile", "view", strtolower($user->name)); ?>"><strong><?php echo $user->name; ?></strong></a> (<?php echo $user->group_name ?>)</td>
                    <td class="border-bottom"></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td valign="top" class="border-right"><img src="<?php echo $user->avatar(); ?>" width="150px"/><br />
                        <?php echo $online; ?><br />
                        <?php if (!$user->anonymous || $acl->Access("x")) { ?>
                            <img src="images/icons/up.gif" style="float:left; margin-top: -4px;" /><?php echo $user->uploaded() ?><br />
                            <img src="images/icons/down.gif" style="float:left; margin-top: -4px;" /><?php echo $user->downloaded() ?>
                        <?php } ?>
                    </td>
                    <td valign="top">
                        <small><?php echo _t("Posted ") ?> <?php echo get_date($db->post_added, "", 0, 0) ?></small><br />
                        <?php
                        echo htmlformat($db->post_content, true);

                        if ($db->post_edited_by != 0) {
                            $edited = new Acl($db->post_edited_by);
                            echo "<br /><br /><small>" . _t("Last edited") . " " . get_date($db->post_edited_date) . " " . _t("by") . " <a href='" . page("profile", "view", cleanurl($edited->name)) . "'>" . $edited->name . "</a></small>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="border-right border-bottom">
                        <a href="<?php echo page("profile", "view", strtolower($user->name)); ?>"><span class="btn"><?php echo _t("Profile") ?></span></a>
                        <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $user->id); ?>"><span class="btn"><?php echo _t("PM") ?></span></a>
                    </td>
                    <td class="border-bottom" align="right">
                        <?php
                        if ($edit) {
                            ?>
                            <a href="<?php echo page("forums", "edit-post", "", "", "", "post=" . $db->post_id); ?>">
                                <span class="btn">Edit</span>
                            </a>
                            <?php
                        }
                        if ($acl->Access("x")) {
                            ?>
                            <a href="<?php echo page("forums", "delete-post", "", "", "", "id=" . $db->post_id . "&confirm"); ?>">
                                <span class="btn red">Delete</span>
                            </a>
                            <?php
                        }
                        ?>

                        <a href="<?php echo page("forums", "quote-post", "", "", "", "post=" . $db->post_id); ?>">
                            <span class="btn">Quote</span>
                        </a>

                    </td>
                </tr>
            </tbody>
        </table>
        <br />
        <?php
    }
    echo $pagemenu;

    $tpl = new Template(PATH_APPLICATIONS . "forums/tpl/");
    $tpl->loadFile("reply.php");
    $tpl->topic_id = $id;
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
