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
        throw new Exception("missing id");

    if (!intval($_GET['torrent']))
        throw new Exception("invalid id");

    $id = $_GET['torrent'];
    $db = new DB("torrents");
    $db->select("torrent_id  = '" . $db->escape($id) . "'");

    if (!$db->numRows())
        throw new Exception("Could not find torrent");

    $db->nextRecord();

    echo "<h4>" . $db->torrent_name . "</h4>";

    $db = new DB("torrents_files");
    $db->setColPrefix("file_");
    $db->setSort("file_name ASC");
    $db->select("file_torrent = '" . $db->escape($id) . "'");

    echo "
        <table width='100%' class='forum' cellspacing='0' cellpadding='5'>
            <thead>
                <tr>
                    <td class='border-bottom border-right' width='80%'>" . _t("Filename") . "</td>
                    <td class='border-bottom'>" . _t("Size") . "</td>
                </tr>
            </thead>
            <tbody>";
    while ($db->nextRecord()) {
        echo "<tr>
                    <td class='border-bottom border-right' width='80%'>" . htmlformat($db->name) . "</td>
                    <td class='border-bottom'>" . bytes($db->size) . "</td>
                </tr>";
    }
    echo "</tbody></table>";
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
