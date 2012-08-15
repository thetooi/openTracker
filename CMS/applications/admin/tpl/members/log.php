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
    if (!$this->userid)
        throw new Exception("User not found");


    if (isset($_POST['log'])) {
        if (empty($_POST['msg']))
            throw new Exception("Cannot add an empty log msg");

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        $db = new DB("users_log");
        $db->setColPrefix("log_");
        $db->user = $this->userid;
        $db->poster = USER_ID;
        $db->added = time();
        $db->msg = $_POST['msg'];
        $db->insert();
    }

    $acl = new Acl($this->userid);
    ?>
    <h4><?php echo _t("Log"); ?></h4>
    <table width="100%">
        <tr>
            <td align="center">
                <form method="POST">
                    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
                    <input type="text" name="msg" size="40" />
                    <input type="submit" name="log" value="Add"><br />
                </form>
            </td>
        </tr>
        <tr>
            <td>
    <?php
    $db = new DB("users_log");
    $db->setColPrefix("log_");
    $db->setSort("log_added DESC");
    $db->select("log_user = '" . $acl->id . "'");
    if ($db->numRows()) {
        while ($db->nextRecord()) {
            $user = new Acl($db->poster);
            ?>
                        <div style="border-bottom: 1px solid #ddd; float:left; width: 100%;">
                            <div style="float:left; padding: 3px;">
                                <a href="<?php echo page("profile", "view", $user->name) ?>"><?php echo $user->name ?></a> -
                            </div>
                            <div style="float:left; padding: 3px;">
            <?php echo htmlformat($db->msg); ?>
                            </div>
                            <div style="float:right; padding: 3px;">
            <?php echo get_date($db->added) ?>
                            </div>
                        </div>
            <?php
        }
    } else {
        echo _t("No Log posts found");
    }
    ?>
            </td>
        </tr>
    </table>


    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>