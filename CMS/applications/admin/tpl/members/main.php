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

?>

<h4><?php echo _t("Members") ?></h4><br />
<a href="<?php echo page("admin", "members", "create"); ?>"><span class="btn blue"><?php echo _t("Create account") ?></span></a>
<br /><br />
<form method="POST">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
    <?php echo _t("Username / Email") ?> <br />
    <input type="search" name="q" size="50" value="<?php echo isset($_POST['q']) ? $_POST['q'] : "" ?>" /><br />
    <br />
    <input type="submit" name="search" value="<?php echo _t("Search") ?>" />
</form>

<?php
if (isset($_POST['search'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        if (empty($_POST['q']))
            throw new Exception();

        $db = new DB("users");
        $db->setColPrefix("user_");
        $db->select("user_name LIKE '%" . $db->escape($_POST['q']) . "%' OR user_email LIKE '%" . $_POST['q'] . "%'");
        if (!$db->numRows())
            throw new Exception("No members found");

        while ($db->nextRecord()) {
            $acl = new Acl($db->id);
            $time = time() - 200;
            $online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
            ?>
            <div class="confirm">
                <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
                <a href="<?php echo page("admin", "members", "edit", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>) <?php echo $online; ?>
                <br />
                <img src="images/icons/up.gif" /> <?php echo $acl->uploaded(); ?><br />
                <img src="images/icons/down.gif" /> <?php echo $acl->downloaded(); ?><br />
                <br />
                <a href="<?php echo page("admin", "members", "edit", $acl->name) ?>"><span class="btn"><?php echo _t("Edit") ?></span></a>
            </div>
            <?php
        }
    } Catch (Exception $e) {
        if ($e->getMessage() != "")
            echo error(_t($e->getMessage()));
    }
}
?>