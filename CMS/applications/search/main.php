<h4>Search</h4>
<form method="GET">
    <?php
    if (!CLEAN_URLS) {
        ?>
        <?php if (isset($this->args['application'])) { ?><input type="hidden" name="application" value="<?php echo $this->args['application'] ?>"><?php } ?>
        <?php if (isset($this->args['action'])) { ?><input type="hidden" name="action" value="<?php echo $this->args['action'] ?>"><?php } ?>
        <?php if (isset($this->args['var_a'])) { ?><input type="hidden" name="var_a" value="<?php echo $this->args['var_a'] ?>"><?php } ?>
        <?php if (isset($this->args['var_b'])) { ?><input type="hidden" name="var_b" value="<?php echo $this->args['var_b'] ?>"><?php } ?>
        <?php if (isset($this->args['var_c'])) { ?><input type="hidden" name="var_c" value="<?php echo $this->args['var_c'] ?>"><?php } ?>
        <?php
    }
    ?>

    <input type="search" name="q" size="50" value="<?php echo isset($_GET['q']) ? $_GET['q'] : "" ?>" /><br />
    <input type="radio" name="type" value="members" <?php echo (!isset($_GET['type']) || $_GET['type'] == "members") ? "CHECKED" : "" ?>> Members <input type="radio" name="type" value="forums" <?php echo (isset($_GET['type']) && $_GET['type'] == "forums") ? "CHECKED" : "" ?>> Forums
    <br />
    <input type="submit" value="Search" />
</form>

<?php
$this->setTitle("Search");

$user = new Acl(USER_ID);

if (isset($_GET['q']) && isset($_GET['type']) && !empty($_GET['q'])) {
    try {

        switch ($_GET['type']) {
            case 'members':

                $db = new DB("users");
                $db->setColPrefix("user_");
                if ($user->Access("x"))
                    $db->select("user_name LIKE '%" . $db->escape($_GET['q']) . "%'");
                else
                    $db->select("user_name LIKE '%" . $db->escape($_GET['q']) . "%' AND user_anonymous = 0");

                if (!$db->numRows())
                    throw new Exception("No members found");

                while ($db->nextRecord()) {
                    $acl = new Acl($db->id);
                    $time = time() - 200;
                    $online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
                    ?>
                    <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
                        <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
                        <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>) <?php echo $online; ?>
                        <br />
                        <?php if (!$acl->anonymous || $user->Access("x")) { ?>
                            <img src="images/icons/up.gif" /> <?php echo $acl->uploaded(); ?><br />
                            <img src="images/icons/down.gif" /> <?php echo $acl->downloaded(); ?><br />
                        <?php } ?>
                        <br />
                        <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>"><span class="btn">PM</span></a> 
                        <a href="<?php echo page("profile", "view", $acl->name) ?>"><span class="btn">Profile</span></a>
                    </div>
                    <?php
                }
                break;

            case 'forums':
                $db = new DB("forum_posts");
                $db->join("left", "{PREFIX}forum_topics", "topic_id", "post_topic");
                $db->select("post_content LIKE('%" . $db->escape($_GET['q']) . "%')");
                if (!$db->numRows())
                    throw new Exception("No forum posts found");
                while ($db->nextRecord()) {
                    $user = new Acl($db->post_user);
                    ?>
                    <h4><a href="<?php echo page("forums", "view-topic", cleanurl($db->topic_subject) . "-" . $db->topic_id, "", "", "page=p" . $db->post_id . "#post" . $db->post_id) ?>"><?php echo $db->topic_subject; ?></a></h4>
                    <table class="forum" id="post<?php echo $db->post_id ?>" width="100%" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr>
                                <td width="150px" class="border-bottom"><a href="<?php echo page("profile", "view", strtolower($user->name)); ?>"><?php echo $user->name; ?></a></td>
                                <td class="border-bottom"></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td valign="top" class="border-right"><img src="<?php echo $user->avatar(); ?>" width="150px"/></td>
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
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br />
                    <?php
                }
                break;
        }
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>