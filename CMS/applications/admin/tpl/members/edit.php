<?php

function addLog($user, $msg) {
    $db = new DB("users_log");
    $db->setColPrefix("log_");
    $db->user = $user;
    $db->poster = USER_ID;
    $db->added = time();
    $db->msg = $msg;
    $db->insert();
}

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

    if (isset($_POST['save'])) {
        $acl = new Acl($this->userid);
        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");


        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            throw new Exception("Invalid email address");


        $db = new DB("users");
        $db->setColPrefix("user_");
        if ($_POST['email'] != $acl->email)
            addLog($this->userid, "changed email");

        if ($_POST['group'] != $acl->group)
            addLog($this->userid, "changed group");

        if ($_POST['status'] != $acl->status)
            addLog($this->userid, "changed account status");

        $db->email = $_POST['email'];
        $db->group = $_POST['group'];
        $db->status = $_POST['status'];
        $db->invites = $_POST['invites'];
        $db->uploader = isset($_POST['uploader']) ? 1 : 0;
        if (!empty($_POST['new_password']) && !empty($_POST['new_password2'])) {
            if ($_POST['new_password'] != $_POST['new_password2'])
                throw new Exception("Passwords did not match");
            $secret = generatePassword();
            $password = $_POST['new_password'];
            $password_hash = md5($secret . $password . $secret);
            $db->password = $password_hash;
            $db->password_secret = $secret;
            addLog($this->userid, "changed password");
        }

        if (isset($_POST['reset_passkey'])) {
            $db->passkey = md5(uniqid(true));
            addLog($this->userid, "reseted passkey");
        }

        $db->update("user_id = '" . $db->escape($this->userid) . "'");

        echo notice(_t("This account has been saved"));
    }

    $acl = new Acl($this->userid);

    $db = new DB("users");
    $db->select("user_id = '$this->userid'");
    $db->nextRecord();
    ?>

    <script>
        $(document).ready(function(){
            $("#avatar_change").change(function(){
                var value = $(this).val(); 
                $("#avatar").attr("src", value);
            });
        });
    </script>

    <form method="POST">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table width="100%">
            <tr>
                <td width="150px" valign="top" align="center">
                    <img src="<?php echo $acl->avatar(); ?>" id="avatar" width="150px"><br />
                </td>
                <td valign="top">
                    <fieldset>
                        <legend><?php echo _t("General"); ?></legend>
                        <table width="100%">
                            <tr>
                                <td width="150px">
                                    <?php echo _t("E-mail"); ?> :
                                </td>
                                <td>
                                    <input type="text" name="email" value="<?php echo $acl->email ?>" size="30" />
                                </td>
                            </tr>

                            <tr>
                                <td width="150px">
                                    <?php echo _t("Status"); ?> :
                                </td>
                                <td>
                                    <select name="status">
                                        <option value="0" <?php echo $acl->status == 0 ? "SELECTED" : "" ?>><?php echo _t("Inactive") ?></option>
                                        <option value="1" <?php echo $acl->status == 1 ? "SELECTED" : "" ?>><?php echo _t("Pending") ?></option>
                                        <option value="3" <?php echo $acl->status == 3 ? "SELECTED" : "" ?>><?php echo _t("Locked") ?></option>
                                        <option value="4" <?php echo $acl->status == 4 ? "SELECTED" : "" ?>><?php echo _t("Active") ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150px">
                                    <?php echo _t("Group"); ?> :
                                </td>
                                <td>
                                    <select name="group"><?php echo getGroups($db->user_group, true) ?></select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150px">
                                    <?php echo _t("Uploader"); ?> :
                                </td>
                                <td>
                                    <input type="checkbox" name="uploader" <?php echo $db->user_uploader == "1" ? "CHECKED" : "" ?>></select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150px">
                                    <?php echo _t("Invites"); ?> :
                                </td>
                                <td>
                                    <input type="text" name="invites" value="<?php echo $acl->invites() ?>" size="5" />
                                </td>
                            </tr>
                            <tr>
                                <td width="150px" valign="top">
                                    <?php echo _t("Passkey"); ?> :
                                </td>
                                <td>
                                    <input type="text" DISABLED value="<?php echo $acl->passkey ?>" size="40" /><br />
                                    <input type="checkbox" name="reset_passkey"><?php echo _t("Reset passkey"); ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo _t("Change password"); ?></legend>
                        <table width="100%">
                            <tr>
                                <td width="150px">
                                    <?php echo _t("New Password"); ?> :
                                </td>
                                <td>
                                    <input type="password" value="" name="new_password" size="30" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo _t("Retype Password"); ?> :
                                </td>
                                <td>
                                    <input type="password" value="" name="new_password2" size="30" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <table width="100%">
                        <tr>
                            <td>
                                <input type="submit" value="<?php echo _t("Save profile"); ?>" name="save" />
                            </td>
                        </tr>
                    </table>
                </td>
        </table>
    </form>

    <fieldset>
        <legend><?php echo _t("Log"); ?></legend>
        <table width="100%">
            <tr>
                <td align="center">
                    <form method="POST">
                        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
                        <input type="text" name="msg" size="40" />
                        <input type="submit" name="log" value="Add"><br />
                        <a href="<?php echo page("admin", "members", "log", $acl->name); ?>"><?php echo _t("View full log") ?></a>
                    </form>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $db = new DB("users_log");
                    $db->setColPrefix("log_");
                    $db->setSort("log_added DESC");
                    $db->setLimit(10);
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
    </fieldset>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>