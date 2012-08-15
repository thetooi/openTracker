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

include("../../init.php");

if(!defined("INCLUDED"))
    die("Access denied");

if (!USER_ID)
    die("access denied");

echo "AJAXOK";

switch ($_POST['action']) {
    case 'update':
        $db = new DB;
        $db->query("DELETE FROM {PREFIX}notifications WHERE notification_unread = '1' AND notification_user = '" . USER_ID . "'");
        $db->query("SELECT COUNT(notification_id) as new_notifi FROM {PREFIX}notifications WHERE notification_unread = '1' AND notification_user = '" . USER_ID . "'");
        $db->nextRecord();
        echo $db->new_notifi;
        break;
}
?>
