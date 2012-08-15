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
/**
 * filename update.php
 * 
 * @author Wuild
 * @package openTracker
 */
define("REVISION", "15");

$system = new DB("system");
$system->setColPrefix("system_");
$system->select();
if (!$system->numRows()) {
    $system->revision = 0;
    $system->insert();
}
$system->nextRecord();
$rev = $system->revision;
$query = array();

if ($rev < 14) {
    $query[] = "ALTER TABLE  `{PREFIX}users` ADD  `user_invited` INT NOT NULL";
}

if ($rev < 15) {
    $query[] = "
CREATE TABLE IF NOT EXISTS `{PREFIX}bonus` (
  `bonus_id` varchar(255) NOT NULL,
  `bonus_title` varchar(255) NOT NULL,
  `bonus_description` text NOT NULL,
  `bonus_type` int(11) NOT NULL,
  `bonus_data` bigint(20) NOT NULL,
  `bonus_cost` int(11) NOT NULL,
  `bonus_sort` int(11) NOT NULL
)  ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;        
";
    $query[] = "ALTER TABLE  `{PREFIX}users` ADD  `user_bonus` FLOAT NOT NULL";
}

if ($system->revision < REVISION) {
    $system->revision = REVISION;
    $system->update();
}

$db = new DB;
if (count($query)) {
    foreach ($query as $sql) {
        $db->query($sql);
    }
}
?>