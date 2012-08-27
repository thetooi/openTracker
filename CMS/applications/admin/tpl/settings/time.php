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

$time = new Pref("time");

try {

    if (isset($_POST['save'])) {
        try {
            $time->offset = $_POST['offset'];
            $time->long = $_POST['time_long'];
            $time->dst = isset($_POST['dst']) ? true : false;
            $time->update();

            echo notice(_t("Time settings saved."));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    ?>
    <form method="POST">
        <table cellspacing="0" width="600px">
            <tr>
                <td width="120px"><?php echo _t("Timezone"); ?>:</td>
                <td><select name="offset"><?php echo timezones($time->offset); ?></select></td>
            </tr>
            <tr>
                <td><?php echo _t("Time format"); ?>:</td>
                <td><input name="time_long" type="text" value="<?php echo $time->long ?>" size="14" /> <?php echo get_date(time(), "", 1, 0) ?></td>
            </tr>
            <tr>
                <td><?php echo _t("Day light saving"); ?>:</td>
                <td><input type="checkbox" name="dst" <?php echo ($time->dst) ? "CHECKED" : "" ?>></td>
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
