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
<h4><?php echo _t("Log in") ?></h4>
<form method="post" action="<?php echo page("user", "login"); ?>">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>" />
    <table>
        <tr><td><?php echo _t("Username"); ?></td></tr>
        <tr><td><input type="text" size="30" name="user_username" /></td></tr>
        <tr><td><?php echo _t("Password"); ?></td></tr>
        <tr><td><input type="password" name="user_password" size="30" /></td></tr>
        <tr><td><label for="remember_me"><input type="checkbox" id="remember_me" name="remeber" /> <?php echo _t("Remember my details"); ?></label></td></tr>
        <tr><td><input type="submit" name="login" value="<?php echo _t("Log in"); ?>" /></td></tr>
    </table>
</form>