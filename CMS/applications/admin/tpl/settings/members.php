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
    $spref = new Pref("system");

    if (isset($_POST['save'])) {
        try {
            $spref->registration = isset($_POST['registration']) ? 1 : 0;
            $spref->max_users = $_POST['max_users'];
            $spref->login_captcha = isset($_POST['login_captcha']) ? 1 : 0;
            $spref->register_captcha = isset($_POST['register_captcha']) ? 1 : 0;
            $spref->update();

            echo notice(_t("Member settings saved."));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }
    ?>
    <form method="POST">
        <table cellspacing="0" width="600px">
            <tr>
                <td width="120px"><?php echo _t("Open registration"); ?></td>
                <td><input type="checkbox" name="registration" <?php echo $spref->registration == 1 ? "CHECKED" : "" ?>></td>
            </tr>     
            <tr>
                <td width="120px"><?php echo _t("Max users"); ?></td>
                <td><input type="text" name="max_users" size="10" value="<?php echo $spref->max_users ?>" /></td>
            </tr>   
            <tr>
                <td width="120px"><?php echo _t("Login captcha"); ?></td>
                <td><input type="checkbox" name="login_captcha" <?php echo ($spref->login_captcha) ? "CHECKED" : "" ?> /></td>
            </tr> 
            <tr>
                <td width="120px"><?php echo _t("Register captcha"); ?></td>
                <td><input type="checkbox" name="register_captcha" <?php echo ($spref->register_captcha) ? "CHECKED" : "" ?> /></td>
            </tr>  
            <tr>
                <td colspan="2"><input type="submit" name="save" value="<?php echo _t("Save settings"); ?>" /></td>
            </tr>
        </table>
    </form>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
