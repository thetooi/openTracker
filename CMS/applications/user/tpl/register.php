<h4><?php echo _t("Register Account"); ?></h4>
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
            <td><?php echo _t("Email"); ?></td>
        </tr>
        <tr>
            <td><input type="text" name="email" size="30" /></td>
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