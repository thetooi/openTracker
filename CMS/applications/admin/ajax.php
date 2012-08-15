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
try {
    include("../../../init.php");

    $acl = new Acl(USER_ID);

    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    echo "AJAXOK";

    if (isset($_POST['action']) && $_POST['action'] == "sort" && isset($_POST['type'])) {
        switch ($_POST['type']) {
            case 'cat':
                $db = new DB;
                $data = explode(",", $_POST['sorting']);
                foreach ($data as $num => $id) {
                    $gid = explode("_", $id);
                    $gid = $gid[1];
                    $db->query("UPDATE {PREFIX}forum_categories SET category_sort = '" . $db->escape($num) . "' WHERE category_id = '" . $db->escape($gid) . "'");
                }
                break;

            case 'forum':
                $db = new DB;
                $data = explode(",", $_POST['sorting']);
                foreach ($data as $num => $id) {
                    $gid = explode("_", $id);
                    $gid = $gid[1];
                    $db->query("UPDATE {PREFIX}forum_forums SET forum_sort = '" . $db->escape($num) . "' WHERE forum_id = '" . $db->escape($gid) . "'");
                }
                break;

            case 'widget':
                $db = new DB;
                $data = explode(",", $_POST['sorting']);
                foreach ($data as $num => $id) {
                    $gid = explode("_", $id);
                    $gid = $gid[1];
                    $db->query("UPDATE {PREFIX}widgets SET widget_sort = '" . $db->escape($num) . "' WHERE widget_id = '" . $db->escape($gid) . "'");
                }
                break;

            case 'navigation':
                $db = new DB;
                $data = explode(",", $_POST['sorting']);
                foreach ($data as $num => $id) {
                    $gid = explode("_", $id);
                    $gid = $gid[1];
                    $db->query("UPDATE {PREFIX}navigations SET navigation_sorting = '" . $db->escape($num) . "' WHERE navigation_id = '" . $db->escape($gid) . "'");
                }
                break;

            case 'bonus':
                $db = new DB;
                $data = explode(",", $_POST['sorting']);
                foreach ($data as $num => $id) {
                    $gid = explode("_", $id);
                    $gid = $gid[1];
                    $db->query("UPDATE {PREFIX}bonus SET bonus_sort = '" . $db->escape($num) . "' WHERE bonus_id = '" . $db->escape($gid) . "'");
                }
                break;
                break;
        }
    }
} Catch (Exception $e) {
    echo $e->getMessage();
}
?>
