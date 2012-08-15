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
 * filename scrape.php
 * 
 * @author Wuild
 * @package openTracker
 */
include("init.php");

$r = 'd5:files';
if (isset($_GET['info_hash'])) {
    $db = new DB("torrents");
    $db->setColPrefix("torrent_");
    $db->select("torrent_info_hash = '" . $db->escape(bin2hex($_GET['info_hash'])) . "'");

    while ($db->nextRecord()) {
        $r .= '20:' . str_pad($db->info_hash, 20) . 'd8:completei' . $db->seeders . 'e10:downloadedi' . $db->times_completed . 'e10:incompletei' . $db->leechers . 'ee';
    }
}

$r .= 'ee';

header('Content-Type: text/plain; charset=UTF-8');
header('Pragma: no-cache');
print($r);
?>
