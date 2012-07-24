<?php

include("../../init.php");

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
