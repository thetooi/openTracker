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

$this->setTitle("Edit Profile");

$acl = new Acl(USER_ID);

if (isset($_POST['save'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        $db = new DB("users");
        $db->setColPrefix("user_");

        $db->torrents_perpage = $_POST['torrents_perpage'];
        $db->posts_perpage = $_POST['posts_perpage'];
        $db->anonymous = isset($_POST['anonymous']) ? 1 : 0;
        $db->language = $_POST['language'];
        if (isset($_POST['cats']))
            $db->default_categories = implode(",", $_POST['cats']);
        else
            $db->default_categories = "";
        if (isset($_POST['reset_passkey']))
            $db->passkey = md5(uniqid(true));

        if (isset($_FILES['avatar']) && !empty($_FILES["avatar"]["name"])) {

            $allowed = array(
                "image/gif",
                "image/jpg",
                "image/jpeg",
                "image/png"
            );
            $type = $_FILES['avatar']['type'];
            if (!in_array($type, $allowed))
                throw new Exception("Invalid image type");

            if ($_FILES["avatar"]["size"] > 1024 * 1024 * 2)
                throw new Exception("Avatar is to big.");

            $ext = end(explode(".", $_FILES["avatar"]["name"]));
            $upload_name = uniqid(true) . "." . $ext;
            $upload_path = PATH_AVATARS . $upload_name;

            if (!is_array(getimagesize($_FILES["avatar"]["tmp_name"])))
                throw new Exception("invalid image data");

            $m = move_uploaded_file($_FILES["avatar"]["tmp_name"], $upload_path);
            if (!$m)
                throw new Exception("Could not upload avatar");

            if ($acl->avatar != "")
                unlink(PATH_AVATARS . $acl->avatar);
            $db->avatar = $upload_name;
        }

        if ($_POST['email'] != $acl->email) {

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                throw new Exception("Invalid email address");

            $body = "Your account has been marked for an email change. follow the link below to confirm this<br />
                <a href='" . page("user", "email-confirm", $acl->name, md5($acl->password_secret), "", "email=" . $_POST['email']) . "'>" . page("user", "email-confirm", $acl->name, md5($acl->password_secret), "", "email=" . $_POST['email']) . "</a>";

            sendEmail($_POST['email'], "Change e-mail", $body);
            echo _t("A confirmation e-mail has been sent to") . " " . $_POST['email'];
        }


        if (isset($_POST['current_password']) && isset($_POST['new_password']) && !empty($_POST['current_password'])) {
            if (md5($acl->password_secret . $_POST['current_password'] . $acl->password_secret) != $acl->password)
                throw new Exception("Wrong password");

            if ($_POST['new_password'] != $_POST['new_password2'])
                throw new Exception("New password did not match");

            $secret = generatePassword();
            $password = $_POST['new_password'];
            $password_hash = md5($secret . $password . $secret);
            $db->password = $password_hash;
            $db->password_secret = $secret;

            $cookie_exp = time() + 31536000;
            $cookie_value = $acl->id . "." . md5("!" . $acl->id . md5("!" . md5($secret . $password . $secret) . md5($_SERVER['REMOTE_ADDR'])));
            setcookie(COOKIE_PREFIX . "user", $cookie_value, $cookie_exp, "/", "", "0");
        }

        $db->update("user_id = '" . USER_ID . "'");

        echo notice(_t("your account has been saved."));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}

$acl = new Acl(USER_ID);
?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
    <table align="center">
        <?php
        $cat = new DB("categories");
        $cat->setColPrefix("category_");
        $cat->select();

        $default = explode(",", $acl->default_categories);

        while ($cat->nextRecord()) {
            $sel = in_array($cat->id, $default) ? " CHECKED" : "";
            ?>
            <td align="center">
                <label for="cat_<?php echo $cat->id ?>"><img src="images/categories/<?php echo $cat->icon; ?>" /><br />
                    <input type="checkbox" name="cats[]" id="cat_<?php echo $cat->id; ?>" value="<?php echo $cat->id ?>" <?php echo $sel; ?> />
                </label>
            </td>
        <?php }
        ?>
    </table>
    <table width="100%">
        <tr>
            <td width="150px" valign="top" align="center">
                <img src="<?php echo $acl->avatar(); ?>" id="avatar" width="150px"><br />
                <fieldset>
                    <legend>Avatar</legend>
                    <input type="file" name="avatar" />
                </fieldset>
            </td>
            <td valign="top">
                <fieldset>
                    <legend><?php echo _t("E-mail"); ?></legend>
                    <table width="100%">
                        <tr>
                            <td width="150px">
                                <?php echo _t("E-mail"); ?> :
                            </td>
                            <td>
                                <input type="text" name="email" value="<?php echo $acl->email ?>" size="30" /><br />
                                <?php echo _t("A confirmation e-mail will be sent to the new e-email address if this is changed."); ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend><?php echo _t("New password"); ?></legend>
                    <table width="100%">
                        <tr>
                            <td width="150px">
                                <?php echo _t("Current Password"); ?> :
                            </td>
                            <td>
                                <input type="password" value="" name="current_password" size="30" />
                            </td>
                        </tr>
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
                <fieldset>
                    <legend><?php echo _t("Additional"); ?></legend>
                    <table width="100%">
                        <tr>
                            <td width="150px">
                                <?php echo _t("Language"); ?> :
                            </td>
                            <td>
                                <select name="language"><?php echo getLanguages($acl->language); ?></select>
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <?php echo _t("Anonymous"); ?> :
                            </td>
                            <td>
                                <input type="checkbox" name="anonymous" <?php echo ($acl->anonymous) ? "CHECKED" : ""; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <?php echo _t("Passkey"); ?> :
                            </td>
                            <td>
                                <input type="text" DISABLED value="<?php echo $acl->passkey ?>" size="40" /><br />
                                <input type="checkbox" name="reset_passkey"><?php echo _t("Reset passkey"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <?php echo _t("Torrents per page"); ?> :
                            </td>
                            <td>
                                <input type="text" name="torrents_perpage" value="<?php echo $acl->torrents_perpage ?>" size="40" /><br />
                                (<?php echo _t("0 = default"); ?>)
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <?php echo _t("Forum posts per page"); ?> :
                            </td>
                            <td>
                                <input type="text" name="posts_perpage" value="<?php echo $acl->posts_perpage ?>" size="40" /><br />
                                (<?php echo _t("0 = default"); ?>)
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

