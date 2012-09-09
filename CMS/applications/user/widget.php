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
<h4><?php echo _t("Users") ?></h4>
<?php
$db = new DB("groups");
$db->setColPrefix("group_");
$db->select("group_id < 10");
echo "<table width='100%'>";
$spref = new Pref("system");
echo "<tr><td><b>Registered users</b></td><td align='right'>".SYSTEM_USERS."</td></tr>";
echo "<tr><td><b>Max users</b></td><td align='right'>".$spref->max_users."</td></tr>";
echo "</table>";
?>
<br />
<h4><?php echo _t("Users online") ?></h4>
<?php
$db = new DB("users");
$db->setCols(array("id", "name"));
$db->setColPrefix("user_");
$time = time() - 300;
$db->select("user_last_access > $time AND user_status = 4");

$user = array();

echo "Users online: ".$db->numRows()."<br />";

if (!$db->numRows())
    echo "No users online.";
while ($db->nextRecord()) {
    $user[] = "<a href='" . page("profile", "view", $db->name) . "'>" . $db->name . "</a>";
}

echo implode(", ", $user);
?>
