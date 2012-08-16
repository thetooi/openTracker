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

    if (!isset($_GET['id']))
        throw new Exception("Missing news id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <center><?php echo _t("Are you sure you wish to delete this?") ?><br /><br />
                <a href="<?php echo page("forums", "delete-topic", "", "", "", "id=" . $_GET['id']) ?>"><span class="btn red"><?php echo _t("Yes"); ?></span></a> 
                <a href="<?php echo page("forums") ?>"><span class="btn"><?php echo _t("No"); ?></span></center></a>
        </div>
        <?php
    } else {
        $db = new DB("forum_topics");
        $db->delete("topic_id = '" . $db->escape($_GET['id']) . "'");
        header("location: " . page("forums"));
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>