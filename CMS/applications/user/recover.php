<?php
$show = true;

$this->setTitle("Recover Password");

if (isset($_POST['recover'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception("Invalid email address");

        $db = new DB("users");
        $db->select("user_email = '" . $db->escape($email) . "'");
        if (!$db->numRows())
            throw new Exception("User not found");
        $db->nextRecord();

        $username = $db->user_name;
        $secret = generatePassword(12);
        $password = generatePassword();
        $password_hash = md5($secret . $password . $secret);

        $db = new DB("users");
        $db->user_password_secret = $secret;
        $db->user_password = $password_hash;
        $db->update("user_email = '" . $db->escape($email) . "'");

        $body = "Hey " . $username . "<br />Someone hopefully you have used the password recovery.<br />If you did not do this, please contact the staff as soon as possible.<br /><br /><b>New Password:</b> " . $password;

        sendEmail($email, "Password recovery", $body);

        $show = false;
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>

<h4><?php echo _t("Recover my account") ?></h4>
<?php
if ($show) {

    echo _t("If you have lost your password you can simply enter your e-mail address,") . "<br />" . _t("And a new password will be sent to you.");
    ?>
    <br />
    <br />
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td><?php echo _t("E-mail") ?></td>
            </tr>
            <tr>
                <td><input type="text" name="email" size="50" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="recover" value="<?php echo _t("Recover my account") ?>" /></td>
            </tr>
        </table>
    </form>
    <?php
} else {
    echo notice(_t("A new password has been sent to " . $email));
}
?>
