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
<h4><?php echo _t("Recover my account") ?></h4>
<?php echo _t("If you have lost your password you can simply enter your e-mail address,") . "<br />" . _t("And a new password will be sent to you."); ?>
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