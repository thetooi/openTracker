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
<h4><?php echo _t("Users online") ?></h4>
<?php
$db = new DB("users");
$db->setCols(array("id", "name"));
$db->setColPrefix("user_");
$time = time() - 300;
$db->select("user_last_access > $time");
if (!$db->numRows())
    echo "No users online.";
while ($db->nextRecord()) {
    echo "<a href='" . page("profile", "view", $db->name) . "'>" . $db->name . "</a> ";
}
?>
