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

<h4><?php echo _t("Send invite") ?></h4>
<?php
try {
    $acl = new Acl(USER_ID);

    $wpref = new Pref("website");

    if ($acl->invites() == 0)
        throw new Exception("Not enough invites available");

    if (isset($_POST['send'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");

            if (empty($_POST['email']))
                throw new Exception("missing email");

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                throw new Exception("Invalid email address");


            $db = new DB("users");
            $db->select("user_email = '" . $db->escape($_POST['email']) . "'");

            if ($db->numRows())
                throw new Exception("Email already exist");

            $passkey = md5(uniqid(true));
            $id = uniqid(true);
            $username = uniqid(true);
            $email = $_POST['email'];

            $password_secret = generatePassword(12);

            $db = new DB("users");
            $db->setColPrefix("user_");
            $db->id = $id;
            $db->name = $username;
            $db->email = $email;
            $db->password_secret = $password_secret;
            $db->status = 1;
            $db->ip = "";
            $db->passkey = $passkey;
            $db->group = 1;
            $db->added = time();
            $db->invited = USER_ID;
            $db->last_login = time();
            $db->last_access = time();
            $db->insert();

            $body = "
                Your friend " . $acl->name . " has invited you to " . $wpref->name . "<br />
                Click the link below to register an account.
                <a href='" . page("user", "invite", "", "", "", "uid=" . $id . "&key=" . md5($password_secret)) . "'>" . page("user", "invite", "", "", "", "uid=" . $id . "&key=" . md5($password_secret)) . "</a>
            ";

            $subject = $wpref->name . " invitation";

            sendEmail($_POST['email'], $subject, $body);
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }
    ?>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td><?php echo _t("E-mail") ?></td>
                <td><input type="text" name="email" size="30"></td>
            </tr>
            <tr>
                <td valign="top"><?php echo _t("Message") ?></td>
                <td><textarea name="message" cols="30" rows="4"></textarea></td>
            </tr>
            <tr>
                <td><input type="submit" name="send" value="<?php echo _t("Send invite") ?>" /></td>
            </tr>
        </table>
    </form>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>