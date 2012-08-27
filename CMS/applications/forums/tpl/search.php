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
    ?>
    <div style="float:right; margin-bottom: 20px;">
        <form method="POST" action="<?php echo page("forums", "search") ?>">
            <input type="text" name="q" size="25" />
            <input type="submit" class="blue" value="<?php echo _t("Search") ?>" />
        </form>
    </div>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
