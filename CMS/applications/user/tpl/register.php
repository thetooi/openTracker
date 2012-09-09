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
        <?php
        $pref = new Pref("system");
        if ($pref->register_captcha) {
            ?>
            <tr><td colspan="2"><img src="CMS/applications/user/captcha/captcha.php" alt="CAPTCHA" /><br /><input type="text" name="captcha"></td></tr>
            <?php
        }
        ?>
        <tr>
            <td><input type="submit" name="register" value="<?php echo _t("Register account"); ?>"></td>
        </tr>
    </table>
</form>