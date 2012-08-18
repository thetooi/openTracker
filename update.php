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
define("REVISION", "18");

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

if ($rev < 16) {
    $query[] = "DROP TABLE IF EXISTS `{PREFIX}peers`;";
    $query[] = "
        CREATE TABLE IF NOT EXISTS `{PREFIX}peers` (
  `peer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `peer_torrent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `peer_passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `peer_peer_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `peer_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `peer_port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `peer_uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_to_go` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_seeder` int(1) NOT NULL,
  `peer_started` int(11) NOT NULL,
  `peer_last_action` int(11) NOT NULL,
  `peer_connectable` int(11) NOT NULL,
  `peer_userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `peer_agent` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `peer_finishedat` int(10) unsigned NOT NULL DEFAULT '0',
  `peer_downloadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_uploadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`peer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
}

if ($rev < 17) {
    $query[] = "CREATE TABLE IF NOT EXISTS `tracker_forum_postread` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_topicid` int(11) NOT NULL,
  `post_lastpostread` int(11) NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
}

if($rev < 18){
    $query[] = "ALTER TABLE  `{PREFIX}forum_topics` ADD  `topic_lastpost` INT NOT NULL";
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