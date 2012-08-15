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

    if (!isset($_GET['torrent']))
        throw new Exception("No id found");

    if (!intval($_GET['torrent']))
        throw new Exception("Invalid id");

    $torrent_id = $_GET['torrent'];

    $torrent = new DB("torrents");
    $torrent->setColPrefix("torrent_");
    $torrent->select("torrent_id = '" . $torrent_id . "'");

    if (!$torrent->numRows())
        throw new Exception("File not found");

    $torrent->nextRecord();

    if (!isset($_GET['passkey']))
        $acl = new Acl(USER_ID);
    else {
        $db = new DB("users");
        $db->setColPrefix("user_");
        $db->select("user_passkey = '" . $db->escape($_GET['passkey']) . "'");
        if (!$db->numRows())
            throw new Exception("user not found");

        $db->nextRecord();
        $acl = new Acl($db->id);
    }

    $fn = PATH_TORRENTS . $torrent->id . ".torrent";

    $dict = Bcode::bdec_file($fn, filesize($fn));

    $dict['value']['announce']['value'] = CMS_URL . "announce.php?passkey=" . $acl->passkey;

    $dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']) . ":" . $dict['value']['announce']['value'];

    $dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);

    header('Content-Disposition: attachment; filename="' . $torrent->filename . '"');

    header("Content-Type: application/x-bittorrent");

    die(Bcode::benc($dict));
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}
?>