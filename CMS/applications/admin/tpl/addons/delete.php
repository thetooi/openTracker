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

try {

    if (!isset($_GET['addon']))
        throw new Exception("Missing data");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="confirm">
            <center><?php echo _t("Are you sure you wish to uninstall this?") ?><br /><br />
                <a href="<?php echo page("admin", "addons", "uninstall", "", "", "addon=" . $_GET['addon']) ?>">
                    <span class="btn red"><?php echo _t("Yes") ?></span>
                </a> 
                <a href="<?php echo page("admin", "addons") ?>">
                    <span class="btn"><?php echo _t("No") ?></span>
                </a>
            </center>
        </div>
        <?
    } else {
        $db = new DB("addons");
        $db->delete("addon_name = '" . $db->escape($_GET['addon']) . "'");
        header("location: " . page("admin", "addons"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
