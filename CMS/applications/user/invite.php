<?php
try {

    if (!isset($_GET['uid']))
        throw new Exception("missing id");

    if (!isset($_GET['key']))
        throw new Exception("missing key");

    $db = new DB("users");
    $db->setColPrefix("user_");
    $db->select("user_id = '" . $db->escape($_GET['uid']) . "' AND user_status = '1'");

    $uid = $_GET['uid'];

    if (!$db->numRows())
        throw new Exception("missing user");

    $db->nextRecord();


    $pref = new Pref("system");

    if (SYSTEM_USERS >= $pref->max_users)
        throw new Exception("We have reached the max amount of users. Try again later");


    if (isset($_POST['register'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");

            if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password2']))
                throw new Exception("Missing information");

            $username = $_POST['username'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if (strlen($username) < 4)
                throw new Exception("Username is to short, minimum 4 characters");

            if (strlen($password) < 6)
                throw new Exception("Password is to short, minimum 6 characters");

            if (!validate_string($username))
                throw new Exception("Invalid characters in the username");

            if ($password != $password2)
                throw new Exception("Passwords did not match");


            $db = new DB("users");
            $db->select("user_name = '" . $username . "'");
            if ($db->numRows())
                throw new Exception("Username does already exist");

            $passkey = md5(uniqid(true));
            $password_secret = generatePassword(12);
            $password_hash = md5($password_secret . $password . $password_secret);


            $db = new DB("users");
            $db->setColPrefix("user_");
            $db->name = $username;
            $db->password = $password_hash;
            $db->password_secret = $password_secret;
            $db->status = 4;
            $db->ip = $_SERVER['REMOTE_ADDR'];
            $db->last_login = time();
            $db->passkey = $passkey;
            $db->group = 1;
            $db->added = time();
            $db->update("user_id = '" . $db->escape($uid) . "'");


            $cookie_exp = time() + 86400;

            $cookie_value = $uid . "." . md5("!" . $uid . md5("!" . $password_hash . md5($_SERVER['REMOTE_ADDR'])));
            setcookie(COOKIE_PREFIX . "user", $cookie_value, $cookie_exp, "/", "", "0");
            header("Location: " . page(START_APP));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }
    ?>
    <h4>Register account</h4>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td><?php echo _t("Username"); ?></td>
            </tr>
            <tr>
                <td><input type="text" name="username" size="30" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Password"); ?></td>
            </tr>
            <tr>
                <td><input type="password" name="password" size="30" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Confirm password"); ?></td>
            </tr>
            <tr>
                <td><input type="password" name="password2" size="30" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="register" value="<?php echo _t("Register account"); ?>"></td>
            </tr>
        </table>
    </form>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
