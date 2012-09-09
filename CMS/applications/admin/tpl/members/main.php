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
?>

<h4><?php echo _t("Members") ?></h4><br />
<form method="POST">
    <div class="col_100">
        <div class="col_25">
            <?php echo _t("Username") ?> <br />
            <input type="text" name="username" size="30" value="<?php echo isset($_POST['q']) ? $_POST['q'] : "" ?>" />
        </div>
        <div class="col_25">
            <?php echo _t("Email") ?> <br />
            <input type="text" name="email" size="30" value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" />
        </div>
        <div class="col_25">
            <?php echo _t("Ip") ?> <br />
            <input type="text" name="ip" size="30" value="<?php echo isset($_POST['ip']) ? $_POST['ip'] : "" ?>" />
        </div>
        <div class="col_25">
            <?php echo _t("Group") ?> <br />
            <select name="group" style="width: 220px;">
                <option value="all">All</option>
                <?php echo getGroups($_POST['group']); ?>
            </select>
        </div>
        <br />
        <div class="col_100">
            <input type="submit" name="search" value="<?php echo _t("Search") ?>" />
        </div>
    </div>
</form>

<table class="admin_table" width="100%">
    <thead>
        <tr>
            <td>Username</td>
            <td>Email</td>
            <td>Group</td>
            <td>IP</td>
            <td></td>
            <td>Downloaded</td>
            <td>Uploaded</td>
            <td>Joined</td>
            <td>Status</td>
        </tr>
    </thead>
    <tbody>
        <?php
        try {
            $db = new DB("users");
            $db->setColPrefix("user_");

            $query = array();

            if (isset($_POST['username']) && !empty($_POST['username']))
                $query[] = "user_name LIKE '%" . $db->escape($_POST['username']) . "%'";

            if (isset($_POST['email']) && !empty($_POST['email']))
                $query[] = "user_email LIKE '%" . $db->escape($_POST['email']) . "%'";

            if (isset($_POST['ip']) && !empty($_POST['ip']))
                $query[] = "user_ip LIKE '%" . $db->escape($_POST['ip']) . "%'";

            if (isset($_POST['group'])) {
                if ($_POST['group'] != "all")
                    $query[] = "user_group = '" . $db->escape($_POST['group']) . "'";
            }
            $db->select(implode(" AND ", $query));
            if (!$db->numRows())
                throw new Exception("No members found");

            while ($db->nextRecord()) {
                $acl = new Acl($db->id);
                $time = time() - 200;
                $online = ($acl->last_access < $time) ? get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";

                switch ($acl->status) {
                    case "1":
                        $status = "Pending";
                        break;

                    case "0":
                        $status = "Inactive";
                        break;

                    case "3":
                        $status = "Locked";
                        break;

                    case "4":
                        $status = "Active";
                        break;
                }
                ?>
                <tr>
                    <td><a href="<?php echo page("admin", "members", "edit", $acl->name) ?> "><?php echo $acl->name; ?></a></td>
                    <td><?php echo $acl->email; ?></td>
                    <td><?php echo $acl->group_name; ?></td>
                    <td><?php echo $acl->ip; ?></td>
                    <td class="dark" align="center">
                        <a href="<?php echo page("admin", "members", "edit", $acl->name) ?> "><img src="siteadmin/images/icons/edit_16.png" title="Edit" /></a>
                        <a href="<?php echo page("admin", "members", "delete", "", "", "id=" . $acl->id . "&confirm") ?> "><img src="siteadmin/images/icons/del_16.png" title="Edit" /></a>
                    </td>
                    <td><?php echo $acl->downloaded(); ?></td>
                    <td><?php echo $acl->uploaded(); ?></td>
                    <td><?php echo get_date($acl->added, "", 1); ?></td>
                    <td><?php echo $status; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
} Catch (Exception $e) {
    if ($e->getMessage() != "")
        echo error(_t($e->getMessage()));
}
?>