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

$this->setTitle("Torrent Details");

try {

    if (!isset($_GET['id']))
        throw new Exception("missing id");

    if(!intval($_GET['id']))
        throw new Exception("invalid id");
    
    $db = new DB("torrents_imdb");
    $db->select("imdb_torrent = '" . $db->escape($_GET['id']) . "'");

    $tpl = new Template(PATH_APPLICATIONS . "torrent/tpl/");
    if ($db->numRows())
        $tpl->loadFile("details_imdb.php");
    else
        $tpl->loadFile("details.php");

    $tpl->build();
} Catch (Exception $e) {
   echo error(_t($e->getMessage()));
}
?>