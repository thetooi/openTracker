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

    $db = new DB("files");
    $db->setColPrefix("file_");
    $db->select("file_id = '" . $db->escape($_GET['id']) . "'");
    if (!$db->numRows())
        throw new Exception("File not found");

    $db->nextRecord();

    $ext = end(explode(".", $db->name));

    $db->delete("file_id = '" . $db->escape($_GET['id']) . "'");
    unlink(PATH_ROOT . "files/" . $db->id . "." . $ext);
    header("location: " . page("admin", "files"));
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
